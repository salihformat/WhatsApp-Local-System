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
        activity('auth')
            ->causedBy($event->user)
            ->withProperties(['ip' => request()->ip()])
            ->log('تسجيل دخول');
    }

    public function handleLogout(Logout $event): void
    {
        if (!$event->user) {
            return;
        }

        activity('auth')
            ->causedBy($event->user)
            ->withProperties(['ip' => request()->ip()])
            ->log('تسجيل خروج');
    }

    public function handleFailedLogin(Failed $event): void
    {
        activity('auth')
            ->withProperties([
                'ip' => request()->ip(),
                'email' => $event->credentials['email'] ?? null,
            ])
            ->log('محاولة دخول فاشلة');
    }
}
