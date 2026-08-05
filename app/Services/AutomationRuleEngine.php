<?php

namespace App\Services;

use App\Jobs\SendMessageJob;
use App\Models\AutomationRule;
use App\Models\Conversation;
use App\Models\InternalNote;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

/**
 * محرك أتمتة عام: يفحص رسالة واردة مقابل قواعد مُعرَّفة من الإدارة، وينفّذ أول إجراء مطابق
 * (تعيين لموظف، إضافة ملاحظة داخلية، أو رد تلقائي فوري). نفس مبدأ PrintRuleEngine لكن للأفعال
 * العامة بدل الطباعة تحديداً.
 */
class AutomationRuleEngine
{
    public function evaluate(Message $message, Conversation $conversation): void
    {
        $rules = AutomationRule::active()->orderedByPriority()->get();

        foreach ($rules as $rule) {
            if ($this->ruleMatches($rule, $message)) {
                $this->executeAction($rule, $message, $conversation);
                // أول قاعدة مطابقة فقط تُنفَّذ (نفس منطق PrintRuleEngine) — لتفادي تعارض عدة
                // إجراءات متضاربة (مثال: رد تلقائي من قاعدتين مختلفتين لنفس الرسالة)
                return;
            }
        }
    }

    private function ruleMatches(AutomationRule $rule, Message $message): bool
    {
        return match ($rule->match_type) {
            'phone_number' => $message->phone_number === $rule->match_value,
            'phone_prefix' => str_starts_with((string) $message->phone_number, $rule->match_value),
            'keyword' => $this->matchesAnyKeyword($rule->match_value, $message->message_text),
            default => false,
        };
    }

    private function matchesAnyKeyword(string $matchValue, ?string $messageText): bool
    {
        if (empty($messageText)) {
            return false;
        }

        foreach (array_filter(array_map('trim', explode(',', $matchValue))) as $keyword) {
            if ($keyword !== '' && mb_stripos($messageText, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    private function executeAction(AutomationRule $rule, Message $message, Conversation $conversation): void
    {
        try {
            match ($rule->action_type) {
                'assign_user' => $this->assignUser($rule, $conversation),
                'internal_note' => $this->addInternalNote($rule, $conversation),
                'auto_reply' => $this->sendAutoReply($rule, $conversation),
                default => null,
            };

            Log::info('AutomationRuleEngine: Rule executed', [
                'rule_id' => $rule->id,
                'rule_name' => $rule->name,
                'action' => $rule->action_type,
                'message_id' => $message->id,
            ]);
        } catch (\Exception $e) {
            Log::error('AutomationRuleEngine: Failed to execute rule', [
                'rule_id' => $rule->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function assignUser(AutomationRule $rule, Conversation $conversation): void
    {
        $userId = (int) $rule->action_value;
        if (!$userId) {
            return;
        }

        $conversation->update(['assigned_to' => $userId]);

        $conversation->activities()->create([
            'type' => 'assigned',
            'description' => "تعيين تلقائي عبر قاعدة الأتمتة: {$rule->name}",
            'properties' => ['assigned_to' => $userId, 'automation_rule_id' => $rule->id],
        ]);
    }

    private function addInternalNote(AutomationRule $rule, Conversation $conversation): void
    {
        // عمود user_id في internal_notes غير قابل للفراغ (NOT NULL) — لا يوجد "مستخدم نظام" حقيقي،
        // فنستخدم أول حساب أدمن كمؤلف صوري مع توضيح صريح في نص الملاحظة أنها آلية المصدر.
        $systemUserId = \App\Models\User::where('is_admin', true)->value('id')
            ?? \App\Models\User::value('id');

        if (!$systemUserId) {
            return;
        }

        InternalNote::create([
            'conversation_id' => $conversation->id,
            'user_id' => $systemUserId,
            'note' => "🤖 [تلقائي عبر: {$rule->name}] " . $rule->action_value,
        ]);
    }

    /** الحد الأقصى لعدد الردود التلقائية المسموح بها لنفس المحادثة خلال نافذة الحماية أدناه */
    private const MAX_AUTO_REPLIES_PER_WINDOW = 3;

    /** نافذة زمنية بالدقائق لعدّ الردود التلقائية الأخيرة */
    private const AUTO_REPLY_WINDOW_MINUTES = 10;

    private function sendAutoReply(AutomationRule $rule, Conversation $conversation): void
    {
        // حماية من حلقة ردود لا نهائية: لو كان الطرف الآخر أيضاً نظاماً آلياً (بوت آخر)، كل رد منه
        // قد يُطابق قاعدة أتمتة هنا فيُنتج رداً جديداً يُحفّز رداً من الطرف الآخر... إلخ. نوقف الرد
        // التلقائي مؤقتاً لهذه المحادثة إن تجاوزنا حداً معقولاً خلال فترة قصيرة.
        $recentAutoReplies = Message::where('conversation_id', $conversation->id)
            ->where('is_incoming', false)
            ->where('created_at', '>=', now()->subMinutes(self::AUTO_REPLY_WINDOW_MINUTES))
            ->where('metadata->source', 'automation_rule')
            ->count();

        if ($recentAutoReplies >= self::MAX_AUTO_REPLIES_PER_WINDOW) {
            Log::warning('AutomationRuleEngine: Auto-reply loop guard triggered, skipping reply', [
                'conversation_id' => $conversation->id,
                'rule_id' => $rule->id,
                'recent_auto_replies' => $recentAutoReplies,
            ]);
            return;
        }

        $reply = Message::create([
            'conversation_id' => $conversation->id,
            'phone_number' => $conversation->phone_number,
            'message_text' => $rule->action_value,
            'message_type' => 'text',
            'is_incoming' => false,
            'status' => 'pending',
            'metadata' => ['source' => 'automation_rule', 'automation_rule_id' => $rule->id],
        ]);

        dispatch(new SendMessageJob($reply->id));

        $conversation->update(['last_message_at' => now()]);
    }
}
