<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPrintJob;
use App\Models\PrintJob;
use App\Services\PrintJobDispatcher;
use Illuminate\Http\Request;

class PrintJobController extends Controller
{
    public function index(Request $request)
    {
        $query = PrintJob::with(['printer', 'message']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('file_name')) {
            $query->where('file_name', 'like', '%' . $request->input('file_name') . '%');
        }

        if ($request->filled('printer_id')) {
            $query->where('printer_id', $request->input('printer_id'));
        }

        if ($request->filled('phone_number')) {
            $query->whereHas('message', function ($q) use ($request) {
                $q->where('phone_number', 'like', '%' . $request->input('phone_number') . '%');
            });
        }

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportCsv($query);
        }

        $printJobs = $query->latest()->paginate(20)->withQueryString();
        $printers = \App\Models\Printer::all();
        $pendingCount = PrintJob::where('status', 'awaiting_approval')->count();

        return view('print-jobs.index', compact('printJobs', 'printers', 'pendingCount'));
    }

    private function exportCsv($query)
    {
        $fileName = 'print_jobs_' . date('Y-m-d_H-i-s') . '.csv';
        $printJobs = $query->latest()->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['رقم المهمة', 'رقم الجوال', 'اسم الملف', 'اسم الطابعة', 'الحالة', 'المحاولات', 'الصفحات', 'وقت الوصول', 'وقت الطباعة', 'المدة', 'رسالة الخطأ'];

        $callback = function() use($printJobs, $columns) {
            $file = fopen('php://output', 'w');
            
            // إضافة BOM لدعم اللغة العربية في ملفات CSV عند فتحها في Excel
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, $columns);

            foreach ($printJobs as $job) {
                $row = [
                    $job->id,
                    $job->message?->phone_number ?? '',
                    $job->file_name,
                    $job->printer?->name ?? '',
                    $job->statusLabel(),
                    $job->attempts,
                    $job->pages ?? '',
                    $job->created_at ? $job->created_at->format('Y-m-d H:i:s') : '',
                    $job->printed_at ? $job->printed_at->format('Y-m-d H:i:s') : '',
                    $job->duration_for_humans ?? '',
                    $job->error_message ?? ''
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function retry(PrintJob $printJob)
    {
        if (!$printJob->canRetry() && $printJob->status !== 'failed') {
            return redirect()->back()->with('error', 'لا يمكن إعادة هذه المهمة');
        }

        $printJob->update(['status' => 'pending', 'error_message' => null]);

        dispatch(new ProcessPrintJob($printJob->id));

        return redirect()->back()->with('success', 'تمت إعادة جدولة مهمة الطباعة');
    }

    public function approve(PrintJob $printJob, PrintJobDispatcher $dispatcher)
    {
        if (!$dispatcher->approve($printJob)) {
            return redirect()->back()->with('error', 'هذه المهمة ليست بانتظار الموافقة حالياً.');
        }

        return redirect()->back()->with('success', "تمت الموافقة، جارٍ طباعة \"{$printJob->file_name}\" الآن.");
    }

    public function reject(PrintJob $printJob, PrintJobDispatcher $dispatcher)
    {
        if (!$dispatcher->reject($printJob, 'تم الرفض من قبل ' . (auth()->user()->name ?? 'مستخدم') . ' من لوحة التحكم.')) {
            return redirect()->back()->with('error', 'هذه المهمة ليست بانتظار الموافقة حالياً.');
        }

        return redirect()->back()->with('success', "تم رفض طباعة \"{$printJob->file_name}\".");
    }

    public function approveAll(Request $request, PrintJobDispatcher $dispatcher)
    {
        $printerId = $request->filled('printer_id') ? (int) $request->input('printer_id') : null;
        $count = $dispatcher->approveAll($printerId);

        return redirect()->back()->with($count > 0 ? 'success' : 'info', $count > 0
            ? "تمت الموافقة على {$count} مهمة طباعة."
            : 'لا توجد مهام بانتظار الموافقة حالياً.');
    }

    public function rejectAll(Request $request, PrintJobDispatcher $dispatcher)
    {
        $printerId = $request->filled('printer_id') ? (int) $request->input('printer_id') : null;

        $query = PrintJob::where('status', 'awaiting_approval');
        if ($printerId) {
            $query->where('printer_id', $printerId);
        }

        $count = 0;
        $userName = auth()->user()->name ?? 'مستخدم';
        foreach ($query->get() as $printJob) {
            if ($dispatcher->reject($printJob, "تم الرفض الجماعي من قبل {$userName} من لوحة التحكم.")) {
                $count++;
            }
        }

        return redirect()->back()->with($count > 0 ? 'success' : 'info', $count > 0
            ? "تم رفض {$count} مهمة طباعة."
            : 'لا توجد مهام بانتظار الموافقة حالياً.');
    }
}
