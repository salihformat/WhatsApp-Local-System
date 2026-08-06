<?php

namespace App\Services;

use App\Models\Message;
use App\Models\PrintRule;
use App\Models\Printer;

/**
 * يحدد الطابعة المناسبة لرسالة واردة بناءً على قواعد مُعرَّفة من المستخدم
 * (رقم الجوال، بادئة الرقم، كلمة مفتاحية في نص الرسالة، أو نوع الملف)،
 * وإلا يستخدم الطابعة الافتراضية إن وُجدت.
 */
class PrintRuleEngine
{
    public function resolvePrinter(Message $message): ?Printer
    {
        if (!$this->isPrintable($message)) {
            return null;
        }

        $rules = PrintRule::with('printer')
            ->active()
            ->orderedByPriority()
            ->get();

        foreach ($rules as $rule) {
            if (!$rule->printer || !$rule->printer->is_active) {
                continue;
            }

            if ($this->ruleMatches($rule, $message)) {
                return $rule->printer;
            }
        }

        return Printer::active()->where('is_default', true)->first();
    }

    /**
     * تدعم الطباعة الآلية PDF والصور (jpg/png/gif/bmp/tiff — يدعمها SumatraPDF مباشرة). ملفات
     * Word/Excel/PowerPoint غير مدعومة بعد (تحتاج تحويلاً مسبقاً لـ PDF، انظر دراسة الجدوى: البند 7).
     * القائمة قابلة للتخصيص عبر printing.printable_extensions.
     */
    private function isPrintable(Message $message): bool
    {
        if ($message->message_type !== 'media' || empty($message->file_path)) {
            return false;
        }

        $allowed = config('printing.printable_extensions', ['pdf']);

        return in_array($this->fileExtension($message), $allowed, true);
    }

    private function ruleMatches(PrintRule $rule, Message $message): bool
    {
        return match ($rule->match_type) {
            'phone_number' => $this->matchesAnyPhoneValue($rule->match_value, (string) $message->phone_number, exact: true),
            'phone_prefix' => $this->matchesAnyPhoneValue($rule->match_value, (string) $message->phone_number, exact: false),
            'keyword' => $this->matchesAnyKeyword($rule->match_value, $message->message_text),
            'file_type' => $this->fileExtension($message) === strtolower(ltrim($rule->match_value, '.')),
            default => false,
        };
    }

    /**
     * يدعم عدة أرقام/بادئات مفصولة بفاصلة في نفس القاعدة (نفس أسلوب دعم عدة كلمات مفتاحية أدناه)،
     * مثال: "966501111111,966502222222" أو بادئات فروع: "96650,96653". تكفي مطابقة رقم واحد فقط.
     */
    private function matchesAnyPhoneValue(string $matchValue, string $phoneNumber, bool $exact): bool
    {
        $values = array_filter(array_map('trim', explode(',', $matchValue)));

        foreach ($values as $value) {
            if ($value === '') {
                continue;
            }
            if ($exact ? $phoneNumber === $value : str_starts_with($phoneNumber, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * يدعم عدة كلمات مفتاحية مفصولة بفاصلة في نفس القاعدة (مثال: "طباعة,اطبع,print")،
     * وتكفي أي واحدة منها كتطابق (منطق OR). المطابقة جزئية (substring) وغير حساسة لحالة الأحرف.
     */
    private function matchesAnyKeyword(string $matchValue, ?string $messageText): bool
    {
        if (empty($messageText)) {
            return false;
        }

        $keywords = array_filter(array_map('trim', explode(',', $matchValue)));

        foreach ($keywords as $keyword) {
            if ($keyword !== '' && mb_stripos($messageText, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * [Fix 2026-08-06] لوحظ فعلياً أن واتساب لا يُرسل اسم ملف إطلاقاً للصور (بخلاف المستندات التي
     * تحمل اسماً صريحاً دائماً)، ورابط التخزين المُرحَّل (S3) يستخدم مُعرِّفاً عشوائياً بلا امتداد —
     * فيفشل استخراج الامتداد من كلا المصدرين، وتُرفض الصورة من الطباعة رغم مطابقتها فعلياً. الحل
     * (FileTypeResolver المشترك) يلجأ لنوع MIME كمصدر أخير موثوق.
     */
    private function fileExtension(Message $message): string
    {
        return FileTypeResolver::resolveExtension($message->file_name, $message->file_path, $message->file_type);
    }
}
