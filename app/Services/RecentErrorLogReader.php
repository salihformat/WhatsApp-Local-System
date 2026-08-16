<?php

namespace App\Services;

/**
 * يقرأ آخر أخطاء PHP الحقيقية (ERROR/CRITICAL) من ملف اللوج المحلي لعرضها في لوحة صحة النظام،
 * بدل الاعتماد على اكتشافها صدفة عبر نص الاستجابة الذي يُعيده هذا النظام للسيرفر المركزي عند فشل
 * تفعيل الويب هوك (كما حدث فعلياً: خطأ Fatal في MessageController لم يظهر إلا داخل جسم رد 500 الذي
 * سجّله السيرفر المركزي). ملف اللوج هنا (storage/logs/laravel.log) قد يصل لعشرات/مئات الميغابايت،
 * لذا نقرأ آخر جزء منه فقط (tail) بدل تحميله كاملاً في الذاكرة.
 */
class RecentErrorLogReader
{
    /**
     * @return array<int, array{timestamp: string, level: string, message: string}>
     */
    public function recent(int $limit = 15, int $tailBytes = 500000): array
    {
        $path = $this->resolveLogPath();
        if (!$path) {
            return [];
        }

        $size = filesize($path);
        $chunk = $this->readTail($path, min($tailBytes, $size));

        // كل إدخال لوج يبدأ بـ "[YYYY-MM-DD HH:MM:SS] channel.LEVEL: ..." — قد يمتد على عدة أسطر
        // (Stack Trace)، لذا نقسّم عند بداية كل إدخال جديد فقط، لا عند كل سطر.
        $entries = preg_split('/(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/', $chunk, -1, PREG_SPLIT_NO_EMPTY);

        $errors = [];
        foreach ($entries as $entry) {
            if (!preg_match('/^\[(?<ts>[^\]]+)\]\s+\S+\.(?<level>ERROR|CRITICAL|EMERGENCY|ALERT):\s+(?<msg>.+?)(\r?\n|$)/', $entry, $m)) {
                continue;
            }

            $errors[] = [
                'timestamp' => $m['ts'],
                'level' => $m['level'],
                'message' => mb_substr(trim($m['msg']), 0, 300),
            ];
        }

        return array_slice(array_reverse($errors), 0, $limit);
    }

    /**
     * يدعم كلا القناتين: single (storage/logs/laravel.log) وdaily (storage/logs/laravel-YYYY-MM-DD.log).
     * مع daily، نبدأ من ملف اليوم؛ إن كان فارغاً أو غير موجود بعد (بداية اليوم مباشرة)، نتراجع لملف
     * الأمس بدل إظهار لوحة فارغة بلا داعٍ.
     */
    private function resolveLogPath(): ?string
    {
        if (config('logging.default') === 'daily') {
            $today = storage_path('logs/laravel-' . now()->format('Y-m-d') . '.log');
            if (is_file($today) && filesize($today) > 0) {
                return $today;
            }

            $yesterday = storage_path('logs/laravel-' . now()->subDay()->format('Y-m-d') . '.log');
            if (is_file($yesterday)) {
                return $yesterday;
            }

            return null;
        }

        $single = storage_path('logs/laravel.log');
        return is_file($single) ? $single : null;
    }

    private function readTail(string $path, int $bytes): string
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return '';
        }

        try {
            $size = filesize($path);
            $seek = max(0, $size - $bytes);
            fseek($handle, $seek);
            return fread($handle, $bytes) ?: '';
        } finally {
            fclose($handle);
        }
    }
}
