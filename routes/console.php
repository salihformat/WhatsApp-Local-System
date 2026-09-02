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

// [Fix] كان هذا يعمل دائماً كل دقيقة بصرف النظر عن وجود queue:work دائم مُشغَّل بالفعل من لوحة
// التحكم (DashboardController::startServices) — تشغيل مستهلِكَين للطابور معاً (Worker دائم + هذا
// المجدوَل) يتسابقان على نفس صفوف جدول jobs يسبب أحياناً SQLSTATE[40001] Deadlock عند محاولة كليهما
// حجز نفس الصف بنفس اللحظة تقريباً، وقد يُعالج كلاهما نفس الرسالة مرتين. نجعله يعمل فقط كشبكة أمان
// احتياطية عندما لا يوجد Worker دائم يعمل فعلياً (بدل تشغيله دائماً بالتوازي معه).
Schedule::command('queue:work --once')->everyMinute()->when(function () {
    $pidFile = storage_path('app/queue_worker.pid');
    if (!file_exists($pidFile)) {
        return true;
    }

    $pid = trim(file_get_contents($pidFile));
    if (!ctype_digit($pid)) {
        return true;
    }

    exec('tasklist /FI "PID eq ' . $pid . '" /NH 2>NUL', $output);
    foreach ($output as $line) {
        if (str_contains($line, $pid)) {
            return false; // الـ Worker الدائم يعمل بالفعل — لا حاجة لهذا الاحتياطي
        }
    }

    return true; // ملف PID قديم/متروك، لا يوجد Worker حي فعلياً
});

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

// تذكير المسؤول تلقائياً بطلبات الموافقة (طباعة/إرسال) المعلّقة منذ فترة طويلة بلا رد
Schedule::command('printing:send-approval-reminders')->everyTenMinutes()->withoutOverlapping();

// نسخ احتياطي يومي لقاعدة البيانات (mysqldump مضغوط)، مع حذف النسخ الأقدم من backup_retention_days
Schedule::command('backup:database')->dailyAt('03:00')->withoutOverlapping();

// تنظيف الملفات اليتيمة في مجلد failed (نجحت رسالتها لاحقاً عبر إعادة محاولة تلقائية فتُنقل لـarchive)
Schedule::command('printmonitor:cleanup-orphans')->hourly()->withoutOverlapping();
