<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\PrintJob;
use App\Models\Printer;
use App\Models\SystemHealthLog;
use App\Services\RecentErrorLogReader;

class SystemHealthController extends Controller
{
    public function index(RecentErrorLogReader $errorLogReader)
    {
        $latest = SystemHealthLog::latest('checked_at')->first();

        $recentChecks = SystemHealthLog::orderByDesc('checked_at')->take(50)->get()->reverse()->values();

        $chartData = [
            'labels' => $recentChecks->map(fn ($log) => $log->checked_at->format('H:i')),
            'pending' => $recentChecks->pluck('pending_messages'),
            'failed' => $recentChecks->pluck('failed_messages'),
            'queue_backlog' => $recentChecks->pluck('queue_backlog_count'),
        ];

        $history = SystemHealthLog::orderByDesc('checked_at')->paginate(20);

        $failedJobs = [];
        try {
            $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->orderByDesc('failed_at')->take(10)->get();
        } catch (\Exception $e) {
            // Table might not exist or error
        }

        // [لوحة صحة الإرسال الموحدة] بخلاف بيانات SystemHealthLog أعلاه (تاريخية، تُسجَّل كل 10 دقائق
        // عبر monitor:system)، هذه بيانات حيّة (لحظة التحميل) عن الطابعات ومهام الطباعة والمراجعة
        // اليدوية — تُكمّل صورة "صحة الإرسال" لتشمل مسار الطباعة أيضاً، وليس فقط رسائل واتساب.
        $printers = Printer::active()->with('fallbackPrinter')->get();
        $unhealthyPrinters = $printers->filter(fn (Printer $p) => $p->last_checked_at && !$p->last_status_healthy);

        $printHealth = [
            'printers' => $printers,
            'unhealthy_count' => $unhealthyPrinters->count(),
            'awaiting_approval_count' => PrintJob::where('status', 'awaiting_approval')->count(),
            'print_failed_today' => PrintJob::where('status', 'failed')->whereDate('updated_at', today())->count(),
        ];

        $sendReview = [
            'review_pending_count' => Message::where('status', 'review_pending')->count(),
        ];

        // أخطاء PHP الفعلية الحديثة (استثناءات، أخطاء fatal) من ملف اللوج المحلي — راجع
        // RecentErrorLogReader لسبب إضافتها هنا تحديداً.
        $recentErrors = $errorLogReader->recent();

        return view('system-health.index', compact('latest', 'chartData', 'history', 'failedJobs', 'printHealth', 'sendReview', 'recentErrors'));
    }

    public function restartQueue()
    {
        try {
            app(\App\Http\Controllers\DashboardController::class)->restartQueue();
            return redirect()->back()->with('success', 'تم إرسال أمر إعادة تشغيل الطابور بنجاح. قد يستغرق الأمر بضع ثوانٍ.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء محاولة إعادة التشغيل: ' . $e->getMessage());
        }
    }

    public function clearQueue()
    {
        try {
            // مسح مهام الطابور
            \Illuminate\Support\Facades\DB::table('jobs')->truncate();
            
            // تحديث حالة الرسائل وعمليات الطباعة المعلقة إلى فشل حتى لا تبقى عالقة
            \App\Models\Message::where('status', 'pending')->update([
                'status' => 'failed', 
                'error_message' => 'تم مسح المهمة من الطابور يدوياً بواسطة المشرف'
            ]);
            
            if (class_exists(\App\Models\PrintJob::class)) {
                \App\Models\PrintJob::where('status', 'pending')->update([
                    'status' => 'failed', 
                    'error_message' => 'تم مسح المهمة من الطابور يدوياً بواسطة المشرف'
                ]);
            }
            
            return redirect()->back()->with('success', 'تم تفريغ الطابور نهائياً وتحديث حالة العناصر المعلقة إلى "فاشلة".');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء مسح الطابور: ' . $e->getMessage());
        }
    }
}
