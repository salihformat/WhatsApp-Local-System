<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * خدمة مزامنة جهات الاتصال مع النظام المركزي
 *
 * تدير عملية المزامنة ثنائية الاتجاه:
 * - رفع جهات الاتصال المحلية الجديدة/المعدّلة إلى المركزي
 * - سحب التحديثات من المركزي وتطبيقها محلياً
 */
class ContactSyncService
{
    private CentralApiService $apiService;
    private int $batchSize;

    public function __construct(CentralApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->batchSize = config('app.contact_sync_batch_size', 50);
    }

    /**
     * تنفيذ دورة مزامنة كاملة لمستخدم محدد
     *
     * @return array نتائج المزامنة (uploaded, downloaded, failed)
     */
    public function syncForUser(int $userId): array
    {
        $results = [
            'uploaded' => 0,
            'updated_remote' => 0,
            'downloaded' => 0,
            'updated_local' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            // المرحلة 1: رفع المحلي إلى المركزي
            $uploadResults = $this->pushLocalChanges($userId);
            $results = array_merge($results, $uploadResults);

            // المرحلة 2: سحب من المركزي إلى المحلي
            $downloadResults = $this->pullRemoteChanges($userId);
            $results['downloaded'] = $downloadResults['downloaded'];
            $results['updated_local'] = $downloadResults['updated_local'];

            Log::info('Contact sync completed for user', [
                'user_id' => $userId,
                'results' => $results,
            ]);

        } catch (Exception $e) {
            Log::error('Contact sync failed for user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * المرحلة 1: رفع جهات الاتصال المحلية الجديدة والمعدّلة إلى المركزي
     */
    private function pushLocalChanges(int $userId): array
    {
        $results = ['uploaded' => 0, 'updated_remote' => 0, 'failed' => 0, 'skipped' => 0, 'errors' => []];

        // جلب جهات الاتصال التي تحتاج مزامنة على دفعات
        Contact::forUser($userId)
            ->needsSync()
            ->with('groups')
            ->chunk($this->batchSize, function ($contacts) use (&$results) {
                foreach ($contacts as $contact) {
                    try {
                        $result = $this->pushSingleContact($contact);

                        if ($result['success']) {
                            if ($contact->sync_status === 'local_only') {
                                $results['uploaded']++;
                            } else {
                                $results['updated_remote']++;
                            }
                        } else {
                            $results['failed']++;
                            $results['errors'][] = "Contact #{$contact->id}: " . ($result['error'] ?? 'Unknown error');
                        }
                    } catch (Exception $e) {
                        $results['failed']++;
                        $contact->markSyncFailed($e->getMessage());

                        Log::warning('Contact push failed', [
                            'contact_id' => $contact->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        return $results;
    }

    /**
     * رفع جهة اتصال واحدة إلى المركزي
     */
    private function pushSingleContact(Contact $contact): array
    {
        $payload = $this->buildContactPayload($contact);

        // تحديد إذا كان إنشاء جديد أو تحديث
        if (!empty($contact->central_id) && trim($contact->central_id) !== '') {
            // تحديث موجود
            $response = $this->apiService->makeApiRequest('PUT', "/contacts/{$contact->central_id}", $payload);
        } else {
            // إنشاء جديد
            $response = $this->apiService->makeApiRequest('POST', '/contacts', $payload);
        }

        if (!empty($response['success'])) {
            $centralId = $response['contact_id'] ?? $response['id'] ?? $contact->central_id;

            if ($centralId) {
                $contact->markAsSynced((int) $centralId);
            } else {
                // في حالة نجاح الطلب لكن بدون معرّف مركزي
                $contact->update([
                    'sync_status' => 'synced',
                    'synced_at' => now(),
                ]);
            }

            return ['success' => true];
        }

        // فشل المزامنة
        $errorMessage = $response['error'] ?? $response['message'] ?? 'Unknown API error';
        $contact->markSyncFailed($errorMessage);

        return [
            'success' => false,
            'error' => $errorMessage,
        ];
    }

    /**
     * المرحلة 2: سحب التحديثات من المركزي وتطبيقها محلياً
     */
    private function pullRemoteChanges(int $userId): array
    {
        $results = ['downloaded' => 0, 'updated_local' => 0];

        try {
            // جلب آخر تاريخ مزامنة
            $lastSync = Contact::forUser($userId)
                ->whereNotNull('synced_at')
                ->max('synced_at');

            $params = ['since' => $lastSync ?? '2000-01-01 00:00:00'];

            $response = $this->apiService->makeApiRequest('GET', '/contacts', $params);

            if (empty($response['success']) || empty($response['contacts'])) {
                return $results;
            }

            foreach ($response['contacts'] as $remoteContact) {
                try {
                    $result = $this->mergeRemoteContact($userId, $remoteContact);
                    if ($result === 'created') $results['downloaded']++;
                    elseif ($result === 'updated') $results['updated_local']++;
                } catch (Exception $e) {
                    Log::warning('Failed to merge remote contact', [
                        'remote_contact' => $remoteContact['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                }
            }

        } catch (Exception $e) {
            Log::error('Pull remote contacts failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }

        return $results;
    }

    /**
     * دمج جهة اتصال واردة من المركزي مع المحلية
     *
     * استراتيجية الدمج:
     * - إذا لم تكن موجودة محلياً → إنشاء جديدة
     * - إذا كانت موجودة ومتزامنة → تحديث البيانات
     * - إذا كانت موجودة وبانتظار المزامنة → الأولوية للنسخة المحلية (الأحدث)
     */
    private function mergeRemoteContact(int $userId, array $remoteData): string
    {
        $phoneNumber = $remoteData['phone_number'] ?? null;
        $centralId = $remoteData['id'] ?? null;

        if (!$phoneNumber || !$centralId) {
            return 'skipped';
        }

        // البحث محلياً: أولاً بالـ central_id، ثم بالهاتف
        $localContact = Contact::forUser($userId)
            ->where(function ($q) use ($centralId, $phoneNumber) {
                $q->where('central_id', $centralId)
                  ->orWhere('phone_number', $phoneNumber);
            })
            ->first();

        if (!$localContact) {
            // إنشاء جديد من البيانات المركزية
            Contact::create([
                'user_id' => $userId,
                'central_id' => $centralId,
                'phone_number' => $phoneNumber,
                'name' => $remoteData['name'] ?? $phoneNumber,
                'file_number' => $remoteData['file_number'] ?? null,
                'email' => $remoteData['email'] ?? null,
                'company_name' => $remoteData['company_name'] ?? null,
                'notes' => $remoteData['notes'] ?? null,
                'tags' => $remoteData['tags'] ?? null,
                'sync_status' => 'synced',
                'synced_at' => now(),
            ]);

            return 'created';
        }

        // موجود محلياً - تحقق هل يجب التحديث
        if (in_array($localContact->sync_status, ['pending_sync', 'local_only'])) {
            // النسخة المحلية أحدث، لا نكتب فوقها
            // لكن نحفظ الـ central_id إذا لم يكن موجوداً
            if (!$localContact->central_id) {
                $localContact->update(['central_id' => $centralId]);
            }
            return 'skipped';
        }

        // تحديث البيانات من المركزي
        $localContact->update([
            'central_id' => $centralId,
            'name' => $remoteData['name'] ?? $localContact->name,
            'file_number' => $remoteData['file_number'] ?? $localContact->file_number,
            'email' => $remoteData['email'] ?? $localContact->email,
            'company_name' => $remoteData['company_name'] ?? $localContact->company_name,
            'sync_status' => 'synced',
            'synced_at' => now(),
        ]);

        // مزامنة المجموعات إذا وُجدت
        if (!empty($remoteData['groups'])) {
            $this->syncRemoteGroups($userId, $localContact, $remoteData['groups']);
        }

        return 'updated';
    }

    /**
     * مزامنة المجموعات الواردة من المركزي
     */
    private function syncRemoteGroups(int $userId, Contact $contact, array $remoteGroups): void
    {
        $groupIds = [];
        foreach ($remoteGroups as $remoteGroup) {
            $groupName = $remoteGroup['name'] ?? null;
            if (!$groupName) continue;

            $group = ContactGroup::firstOrCreate(
                ['user_id' => $userId, 'name' => $groupName],
                ['color' => $remoteGroup['color'] ?? '#6366f1', 'description' => $remoteGroup['description'] ?? null]
            );
            $groupIds[] = $group->id;
        }

        if (!empty($groupIds)) {
            // دمج بدلاً من استبدال
            $contact->groups()->syncWithoutDetaching($groupIds);
        }
    }

    /**
     * بناء بيانات جهة الاتصال للإرسال إلى المركزي
     */
    private function buildContactPayload(Contact $contact): array
    {
        $payload = [
            'phone_number' => $contact->phone_number,
            'name' => $contact->name,
            'file_number' => $contact->file_number,
            'email' => $contact->email,
            'company_name' => $contact->company_name,
            'notes' => $contact->notes,
            'tags' => $contact->tags,
            'is_favorite' => $contact->is_favorite,
            'custom_fields' => $contact->custom_fields,
        ];

        // إضافة المجموعات
        if ($contact->relationLoaded('groups')) {
            $payload['groups'] = $contact->groups->map(fn ($g) => [
                'name' => $g->name,
                'color' => $g->color,
            ])->toArray();
        }

        return $payload;
    }
}
