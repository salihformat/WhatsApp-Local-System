<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Message;
use App\Jobs\SendMessageJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// إعادة إرسال الرسائل الفاشلة والمعلقة كل 5 دقائق
Schedule::call(function () {
    $messagesToRetry = Message::whereIn('status', ['failed', 'no_whatsapp', 'pending'])
        ->where('retry_count', '<', 5)
        ->where(function ($query) {
            $query->whereNull('sent_at')
                  ->orWhere('sent_at', '<', now()->subMinutes(5));
        })
        ->get();

    foreach ($messagesToRetry as $message) {
        dispatch(new SendMessageJob($message->id));
    }
})->everyFiveMinutes();

// مزامنة حالات الرسائل مع النظام المركزي
Schedule::command('messages:sync-status')->everyTwoMinutes();

// فحص صحة النظام
Schedule::command('monitor:system --interval=0')->everyTenMinutes()->withoutOverlapping();

// تشغيل queue worker كل دقيقة
Schedule::command('queue:work --once')->everyMinute();

// مراقبة مجلد الفواتير (PrintMonitor)
Schedule::command('monitor:folder')->everyMinute()->withoutOverlapping();

// إنشاء تقرير يومي (مرة واحدة فقط)
// Schedule::command('reports:daily')->dailyAt('23:59');

// مزامنة جهات الاتصال مع النظام المركزي كل 15 دقيقة
Schedule::command('contacts:sync')->everyFifteenMinutes()->withoutOverlapping();

// حذف الملفات القديمة من الأرشيف والمجلدات الفاشلة يومياً
Schedule::command('files:clean-old')->daily()->withoutOverlapping();

// مزامنة سياسة التخزين وإعدادات الشركة من النظام المركزي دورياً (لمنع بقاء الإعدادات قديمة إلى الأبد)
Schedule::command('local-system:sync-config')->hourly()->withoutOverlapping();

// مراقبة حالة الطابعات (متصلة/ورق/حبر) وتنبيه عند تغيّر الحالة
Schedule::command('monitor:printers')->everyTenMinutes()->withoutOverlapping();
