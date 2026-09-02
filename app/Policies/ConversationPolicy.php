<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

/**
 * سياسة صلاحيات المحادثات
 * تضمن أن كل مستخدم يصل فقط لمحادثاته الخاصة
 * المدير (Admin) يمكنه الوصول لكل المحادثات
 */
class ConversationPolicy
{
    /**
     * عرض قائمة المحادثات - مسموح لكل المستخدمين المسجلين
     * (الفلترة تتم في Controller)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * عرض محادثة محددة - فقط المالك أو المُعيّن أو المدير
     */
    public function view(User $user, Conversation $conversation): bool
    {
        if ($user->isSupervisor()) {
            return true;
        }

        return $conversation->user_id === $user->id
            || $conversation->assigned_to === $user->id;
    }

    /**
     * إغلاق محادثة - فقط المالك أو المُعيّن أو المدير
     */
    public function close(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }

    /**
     * إرسال رسالة في محادثة - فقط المالك أو المُعيّن أو المدير
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }

    /**
     * إسناد (تعيين) محادثة لوكيل - مسموح للمشرفين والمدراء فقط
     */
    public function assign(User $user, Conversation $conversation): bool
    {
        return $user->isSupervisor();
    }
}
