<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use App\Services\PrinterMonitorService;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function index()
    {
        $printers = Printer::withCount('printJobs')->latest()->get();
        return view('printers.index', compact('printers'));
    }

    public function checkNow(Printer $printer, PrinterMonitorService $monitor)
    {
        $result = $monitor->check($printer);

        $printer->update([
            'last_status' => $result['status'],
            'last_status_healthy' => $result['is_healthy'],
            'last_status_detail' => $result['detail'],
            'last_checked_at' => now(),
        ]);

        $printer->statusLogs()->create([
            'status' => $result['status'],
            'is_healthy' => $result['is_healthy'],
            'detail' => $result['detail'],
            'status_changed' => false,
        ]);

        return redirect()->route('printers.index')
            ->with($result['is_healthy'] ? 'success' : 'warning', "نتيجة فحص \"{$printer->name}\": {$result['detail']}");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'windows_printer_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:document,thermal'],
            'print_mode' => ['nullable', 'string', 'in:auto,approval'],
            'is_default' => ['nullable', 'boolean'],
            'supports_status_check' => ['nullable', 'boolean'],
            'fallback_printer_id' => ['nullable', 'integer', 'exists:printers,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['print_mode'] = $validated['print_mode'] ?? 'auto';
        $validated['is_default'] = $request->boolean('is_default');
        $validated['supports_status_check'] = $request->boolean('supports_status_check');

        if ($validated['is_default']) {
            Printer::where('is_default', true)->update(['is_default' => false]);
        }

        Printer::create($validated);

        return redirect()->route('printers.index')->with('success', 'تمت إضافة الطابعة بنجاح');
    }

    public function update(Request $request, Printer $printer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'windows_printer_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:document,thermal'],
            'print_mode' => ['nullable', 'string', 'in:auto,approval'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'supports_status_check' => ['nullable', 'boolean'],
            'fallback_printer_id' => [
                'nullable', 'integer', 'exists:printers,id',
                function ($attribute, $value, $fail) use ($printer) {
                    if ((int) $value === $printer->id) {
                        $fail('لا يمكن أن تكون الطابعة احتياطية لنفسها.');
                    }
                },
            ],
            'notes' => ['nullable', 'string'],
        ]);

        // [ملاحظة] بخلاف store()، لا نفرض 'auto' هنا إن غاب print_mode من الطلب — بعض نماذج التحديث
        // الجزئية في الواجهة (تبديل الافتراضية/التفعيل...) لا ترسله، فنُبقي القيمة الحالية بدل تصفير
        // وضع الموافقة المضبوط مسبقاً بالخطأ. العمود غير قابل لـ NULL، فلا يجوز تركه فارغاً أيضاً.
        $validated['print_mode'] = $request->filled('print_mode') ? $validated['print_mode'] : $printer->print_mode;
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['supports_status_check'] = $request->boolean('supports_status_check');

        if ($validated['is_default']) {
            Printer::where('id', '!=', $printer->id)->where('is_default', true)->update(['is_default' => false]);
        }

        $printer->update($validated);

        return redirect()->route('printers.index')->with('success', 'تم تحديث الطابعة بنجاح');
    }

    public function destroy(Printer $printer)
    {
        $printer->delete();

        return redirect()->route('printers.index')->with('success', 'تم حذف الطابعة بنجاح');
    }
}
