<?php

namespace App\Http\Controllers;

use App\Models\SystemHealthLog;

class SystemHealthController extends Controller
{
    public function index()
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

        return view('system-health.index', compact('latest', 'chartData', 'history', 'failedJobs'));
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
