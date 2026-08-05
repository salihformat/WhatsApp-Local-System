<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class FailedJobController extends Controller
{
    /**
     * عرض قائمة المهام الفاشلة
     */
    public function index(Request $request)
    {
        if (!Schema::hasTable('failed_jobs')) {
            return redirect()->route('system-health.index')->with('warning', 'جدول المهام الفاشلة غير موجود في قاعدة البيانات.');
        }

        $query = DB::table('failed_jobs')->orderBy('failed_at', 'desc');

        // الفلترة حسب الطابور
        if ($request->filled('queue')) {
            $query->where('queue', $request->queue);
        }

        // التصدير إلى Excel
        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportCsv($query);
        }

        $failedJobs = $query->paginate(20)->withQueryString();

        // جلب قائمة أسماء الطوابير للفلتر
        $queues = DB::table('failed_jobs')->select('queue')->distinct()->pluck('queue');

        return view('failed-jobs.index', compact('failedJobs', 'queues'));
    }

    /**
     * إعادة محاولة مهمة فردية
     */
    public function retry($id)
    {
        try {
            Artisan::call('queue:retry', ['id' => $id]);
            return redirect()->back()->with('success', 'تم إعادة محاولة المهمة بنجاح.');
        } catch (\Exception $e) {
            Log::error('Failed Job Retry Error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء محاولة إعادة المهمة: ' . $e->getMessage());
        }
    }

    /**
     * مسح مهمة فردية نهائياً
     */
    public function forget($id)
    {
        try {
            Artisan::call('queue:forget', ['id' => $id]);
            return redirect()->back()->with('success', 'تم مسح المهمة من السجل بنجاح.');
        } catch (\Exception $e) {
            Log::error('Failed Job Forget Error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء محاولة مسح المهمة: ' . $e->getMessage());
        }
    }

    /**
     * إعادة محاولة كافة المهام
     */
    public function retryAll()
    {
        try {
            Artisan::call('queue:retry', ['id' => 'all']);
            return redirect()->back()->with('success', 'تم البدء بإعادة محاولة كافة المهام الفاشلة.');
        } catch (\Exception $e) {
            Log::error('Failed Jobs Retry All Error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء محاولة إعادة المهام: ' . $e->getMessage());
        }
    }

    /**
     * مسح كافة المهام
     */
    public function flush()
    {
        try {
            Artisan::call('queue:flush');
            return redirect()->back()->with('success', 'تم تفريغ سجل المهام الفاشلة بالكامل.');
        } catch (\Exception $e) {
            Log::error('Failed Jobs Flush Error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء مسح السجل: ' . $e->getMessage());
        }
    }

    private function exportCsv($query)
    {
        $fileName = 'failed_jobs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['المعرف', 'الطابور', 'وقت الفشل', 'الخطأ التقني'];

        $callback = function() use($query, $columns) {
            $file = fopen('php://output', 'w');
            
            // BOM for Arabic Excel
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns);

            $query->chunk(500, function($jobs) use($file) {
                foreach ($jobs as $job) {
                    $row = [
                        $job->id,
                        $job->queue,
                        $job->failed_at,
                        $job->exception
                    ];
                    fputcsv($file, $row);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
