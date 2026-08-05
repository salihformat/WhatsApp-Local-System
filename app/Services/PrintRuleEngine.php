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
     * حالياً تدعم الطباعة الآلية ملفات PDF فقط (انظر دراسة الجدوى: البند 7)
     */
    private function isPrintable(Message $message): bool
    {
        if ($message->message_type !== 'media' || empty($message->file_path)) {
            return false;
        }

        return $this->fileExtension($message) === 'pdf';
    }

    private function ruleMatches(PrintRule $rule, Message $message): bool
    {
        return match ($rule->match_type) {
            'phone_number' => $message->phone_number === $rule->match_value,
            'phone_prefix' => str_starts_with((string) $message->phone_number, $rule->match_value),
            'keyword' => $this->matchesAnyKeyword($rule->match_value, $message->message_text),
            'file_type' => $this->fileExtension($message) === strtolower(ltrim($rule->match_value, '.')),
            default => false,
        };
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

    private function fileExtension(Message $message): string
    {
        $path = parse_url($message->file_path, PHP_URL_PATH) ?: $message->file_path;
        return strtolower(pathinfo($message->file_name ?: $path, PATHINFO_EXTENSION));
    }
}
