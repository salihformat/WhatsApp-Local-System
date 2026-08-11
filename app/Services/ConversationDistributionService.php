<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * يحدد الموظف الذي تُسنَد له محادثة واتساب جديدة تلقائياً عند وصولها، حسب وضع التوزيع المضبوط من
 * صفحة الإعدادات (config('app.conversation_distribution_mode')):
 * - manual: بلا تعيين تلقائي إطلاقاً (يبقى resolveAssignee يُعيد null دوماً).
 * - specific: توزيع عادل بين مجموعة مستخدمين محددة (conversation_distribution_user_ids).
 * - all: نفس التوزيع العادل، لكن بين كل موظفي role=agent المتاحين للتعيين.
 *
 * "العدالة" هنا تعني: يذهب كل طلب لمن لديه حالياً أقل عدد محادثات مفتوحة (status=open) من
 * المجموعة المؤهّلة — وليس تناوباً دورياً بسيطاً (round-robin) لا يراعي تراكم محادثات من موظف
 * معيّن (إجازة/بطء) بينما تتراكم لديه أعباء تلقائياً. هذا الأسلوب يتصحّح ذاتياً بلا حاجة لأي حالة
 * محفوظة (cursor) عبر الطلبات، ويتصرف كتناوب طبيعي عندما تتساوى الأعداد (تُفضَّل حينها أقل ID).
 */
class ConversationDistributionService
{
    public function resolveAssignee(): ?int
    {
        $mode = config('app.conversation_distribution_mode', 'manual');

        $eligibleUserIds = match ($mode) {
            'specific' => $this->specificUserIds(),
            'all' => User::availableForAssignment()->where('role', 'agent')->pluck('id')->all(),
            default => [],
        };

        if (empty($eligibleUserIds)) {
            return null;
        }

        return $this->leastLoadedUserId($eligibleUserIds);
    }

    /**
     * قائمة المستخدمين المحددين في الإعدادات (conversation_distribution_user_ids)، بعد استبعاد أي
     * معرف لمستخدم غير موجود أو غير متاح للتعيين حالياً (إجازة/انشغال مؤقت — راجع
     * User::is_available_for_assignment) — بلا حاجة لتعديل قائمة الإعدادات نفسها لاستثنائه مؤقتاً.
     */
    private function specificUserIds(): array
    {
        $configuredIds = array_filter(array_map('trim', explode(',', (string) config('app.conversation_distribution_user_ids', ''))));
        if (empty($configuredIds)) {
            return [];
        }

        return User::availableForAssignment()->whereIn('id', $configuredIds)->pluck('id')->all();
    }

    /**
     * يختار من بين المعرّفات المؤهّلة صاحب أقل عدد محادثات مفتوحة حالياً (assigned_to + status=open).
     * عند التعادل (شائع جداً عند بداية التشغيل حيث الجميع صفر) يُفضَّل الأصغر ID، فيتصرف النظام
     * كتناوب دوري طبيعي في البداية ثم يتحول تلقائياً لموازنة حمل حقيقية بمجرد اختلاف الأعداد.
     */
    private function leastLoadedUserId(array $eligibleUserIds): int
    {
        $openCounts = Conversation::query()
            ->select('assigned_to', DB::raw('count(*) as open_count'))
            ->whereIn('assigned_to', $eligibleUserIds)
            ->where('status', 'open')
            ->groupBy('assigned_to')
            ->pluck('open_count', 'assigned_to');

        $sortedIds = $eligibleUserIds;
        sort($sortedIds);

        $bestUserId = $sortedIds[0];
        $bestCount = $openCounts[$bestUserId] ?? 0;

        foreach ($sortedIds as $userId) {
            $count = $openCounts[$userId] ?? 0;
            if ($count < $bestCount) {
                $bestUserId = $userId;
                $bestCount = $count;
            }
        }

        return (int) $bestUserId;
    }
}
