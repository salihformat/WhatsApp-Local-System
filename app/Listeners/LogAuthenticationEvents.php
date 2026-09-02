<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

/**
 * يسجّل أحداث الدخول/الخروج ومحاولات الدخول الفاشلة في سجل التدقيق (Audit Log)
 */
class LogAuthenticationEvents
{
    public function handleLogin(Login $event): void
    {
        if ($event->user instanceof \App\Models\User) {
            $event->user->last_login_at = now();
            $event->user->save();
        }

        activity('auth')
            ->causedBy($event->user)
            ->withProperties(['ip' => request()->ip()])
            ->log(__('local_agent.audit_event_login'));
    }

    public function handleLogout(Logout $event): void
    {
        if (!$event->user) {
            return;
        }

        activity('auth')
            ->causedBy($event->user)
            ->withProperties(['ip' => request()->ip()])
            ->log(__('local_agent.audit_event_logout'));
    }

    public function handleFailedLogin(Failed $event): void
    {
        activity('auth')
            ->withProperties([
                'ip' => request()->ip(),
                'email' => $event->credentials['email'] ?? null,
            ])
            ->log(__('local_agent.audit_event_failed_login'));
    }
}
