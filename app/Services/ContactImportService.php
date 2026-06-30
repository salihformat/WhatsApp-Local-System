<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ContactImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class ContactImportService
{
    /**
     * الأعمدة المتاحة لربط الحقول
     */
    public const AVAILABLE_FIELDS = [
        'name' => 'اسم العميل',
        'phone_number' => 'رقم الهاتف',
        'file_number' => 'رقم الملف',
        'email' => 'البريد الإلكتروني',
        'notes' => 'ملاحظات',
    ];

    /**
     * قراءة ومعاينة الملف المرفوع (أول 5 صفوف + أسماء الأعمدة)
     *
     * @param string $filePath
     * @return array ['headers' => [...], 'preview' => [...], 'total_rows' => int]
     */
    public function previewFile(string $filePath): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, true);

            if (empty($rows)) {
                throw new Exception('الملف فارغ');
            }

            $headers = array_values(array_shift($rows)); // الصف الأول = العناوين
            $preview = array_slice(array_values($rows), 0, 5); // معاينة 5 صفوف

            return [
                'headers' => $headers,
                'preview' => $preview,
                'total_rows' => count($rows),
            ];
        } catch (Exception $e) {
            Log::error('Error previewing import file', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * تنفيذ عملية الاستيراد
     *
     * @param ContactImport $import
     * @param string $duplicateHandling 'skip' أو 'update'
     * @return array ['success_count', 'failed_count', 'duplicate_count', 'updated_count', 'errors']
     */
    public function processImport(ContactImport $import, string $duplicateHandling = 'skip'): array
    {
        $results = [
            'success_count' => 0,
            'failed_count' => 0,
            'duplicate_count' => 0,
            'updated_count' => 0,
            'errors' => [],
        ];

        try {
            $import->update(['status' => 'processing']);

            $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($import->file_path);
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, true);

            if (empty($rows)) {
                throw new Exception('الملف فارغ');
            }

            // إزالة صف العناوين
            array_shift($rows);

            $columnMapping = $import->column_mapping;
            $userId = $import->user_id;
            $groupId = $import->contact_group_id;

            $import->update(['total_rows' => count($rows)]);

            // معالجة كل صف
            foreach ($rows as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // +2 لأن الصف الأول عناوين وindex يبدأ من 0
                $rowValues = array_values($row);

                try {
                    $contactData = $this->mapRowToContactData($rowValues, $columnMapping);

                    // التحقق من الحقول المطلوبة
                    if (empty($contactData['phone_number'])) {
                        $results['failed_count']++;
                        $results['errors'][] = [
                            'row' => $rowNumber,
                            'error' => 'رقم الهاتف مطلوب',
                        ];
                        continue;
                    }

                    if (empty($contactData['name'])) {
                        $results['failed_count']++;
                        $results['errors'][] = [
                            'row' => $rowNumber,
                            'error' => 'اسم العميل مطلوب',
                        ];
                        continue;
                    }

                    // تنظيف رقم الهاتف
                    $contactData['phone_number'] = $this->normalizePhoneNumber($contactData['phone_number']);

                    // التحقق من التكرار
                    $existingContact = Contact::where('user_id', $userId)
                        ->where('phone_number', $contactData['phone_number'])
                        ->first();

                    if ($existingContact) {
                        if ($duplicateHandling === 'update') {
                            // تحديث البيانات الموجودة
                            $existingContact->update(array_filter($contactData));
                            $existingContact->markPendingSync();

                            // إضافة للمجموعة إذا تم تحديدها
                            if ($groupId && !$existingContact->groups()->where('contact_groups.id', $groupId)->exists()) {
                                $existingContact->groups()->attach($groupId);
                            }

                            $results['updated_count']++;
                        } else {
                            $results['duplicate_count']++;
                        }
                        continue;
                    }

                    // إنشاء جهة اتصال جديدة
                    $contactData['user_id'] = $userId;
                    $contactData['sync_status'] = 'pending_sync';

                    $contact = Contact::create($contactData);

                    // إضافة للمجموعة
                    if ($groupId) {
                        $contact->groups()->attach($groupId);
                    }

                    $results['success_count']++;

                } catch (Exception $e) {
                    $results['failed_count']++;
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            // تحديث سجل الاستيراد
            $import->update([
                'status' => 'completed',
                'success_count' => $results['success_count'],
                'failed_count' => $results['failed_count'],
                'duplicate_count' => $results['duplicate_count'],
                'updated_count' => $results['updated_count'],
                'error_log' => !empty($results['errors']) ? $results['errors'] : null,
                'completed_at' => now(),
            ]);

            // Dispatch sync job automatically
            if ($results['success_count'] > 0 || $results['updated_count'] > 0) {
                \App\Jobs\SyncContactsJob::dispatch($userId);
            }

        } catch (Exception $e) {
            Log::error('Import process failed', [
                'import_id' => $import->id,
                'error' => $e->getMessage(),
            ]);

            $import->update([
                'status' => 'failed',
                'error_log' => [['error' => $e->getMessage()]],
            ]);

            throw $e;
        }

        return $results;
    }

    /**
     * ربط صف من الملف بحقول جهة الاتصال
     */
    private function mapRowToContactData(array $rowValues, array $columnMapping): array
    {
        $data = [];

        foreach ($columnMapping as $fieldName => $columnIndex) {
            if ($columnIndex === null || $columnIndex === '' || $columnIndex === '-1') {
                continue;
            }

            $index = (int) $columnIndex;
            $value = trim($rowValues[$index] ?? '');

            if ($value !== '') {
                $data[$fieldName] = $value;
            }
        }

        return $data;
    }

    /**
     * تنظيف وتنسيق رقم الهاتف
     * - إزالة المسافات والأحرف الخاصة
     * - التحويل من صيغة محلية إلى صيغة دولية (966)
     */
    public function normalizePhoneNumber(string $phone): string
    {
        // إزالة كل شيء عدا الأرقام وعلامة +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // إزالة + من البداية إذا وجدت
        $phone = ltrim($phone, '+');

        // تحويل الأرقام المحلية السعودية
        if (str_starts_with($phone, '05')) {
            $phone = '966' . substr($phone, 1);
        } elseif (str_starts_with($phone, '5') && strlen($phone) === 9) {
            $phone = '966' . $phone;
        }

        return $phone;
    }

    /**
     * إنشاء ملف نموذج Excel للتحميل
     */
    public function generateTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('جهات الاتصال');

        // رأس الأعمدة
        $headers = ['اسم العميل', 'رقم الهاتف', 'رقم الملف', 'البريد الإلكتروني', 'ملاحظات'];
        foreach ($headers as $index => $header) {
            $col = chr(65 + $index); // A, B, C...
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        // تنسيق رأس الأعمدة
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '128C7E']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // صفوف مثال
        $sheet->setCellValue('A2', 'أحمد محمد');
        $sheet->setCellValue('B2', '966501234567');
        $sheet->setCellValue('C2', 'F-001');
        $sheet->setCellValue('D2', 'ahmed@example.com');
        $sheet->setCellValue('E2', 'عميل VIP');

        $sheet->setCellValue('A3', 'سارة أحمد');
        $sheet->setCellValue('B3', '0512345678');
        $sheet->setCellValue('C3', 'F-002');

        // RTL
        $sheet->setRightToLeft(true);

        // حفظ الملف المؤقت
        $tempPath = storage_path('app/temp/contacts_template.xlsx');
        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * تصدير جهات الاتصال إلى ملف Excel
     */
    public function exportContacts($contacts): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('جهات الاتصال');

        // العناوين
        $headers = ['اسم العميل', 'رقم الهاتف', 'رقم الملف', 'البريد الإلكتروني', 'المجموعات', 'ملاحظات', 'عدد الرسائل', 'تاريخ الإنشاء'];
        foreach ($headers as $index => $header) {
            $col = chr(65 + $index);
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        // تنسيق الرأس
        $lastCol = chr(64 + count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '128C7E']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // البيانات
        $row = 2;
        foreach ($contacts as $contact) {
            $groups = $contact->groups->pluck('name')->implode(', ');
            $sheet->setCellValue("A{$row}", $contact->name);
            $sheet->setCellValue("B{$row}", $contact->phone_number);
            $sheet->setCellValue("C{$row}", $contact->file_number);
            $sheet->setCellValue("D{$row}", $contact->email);
            $sheet->setCellValue("E{$row}", $groups);
            $sheet->setCellValue("F{$row}", $contact->notes);
            $sheet->setCellValue("G{$row}", $contact->total_messages);
            $sheet->setCellValue("H{$row}", $contact->created_at->format('Y-m-d H:i'));
            $row++;
        }

        $sheet->setRightToLeft(true);

        $tempPath = storage_path('app/temp/contacts_export_' . now()->format('Y_m_d_His') . '.xlsx');
        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }
}
