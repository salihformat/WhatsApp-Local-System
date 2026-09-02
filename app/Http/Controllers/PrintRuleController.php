<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use App\Models\PrintRule;
use Illuminate\Http\Request;

class PrintRuleController extends Controller
{
    public function index()
    {
        $rules = PrintRule::with('printer')->orderedByPriority()->get();
        $printers = Printer::active()->orderBy('name')->get();
        return view('print-rules.index', compact('rules', 'printers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'min:0'],
            'match_type' => ['required', 'string', 'in:' . implode(',', PrintRule::MATCH_TYPES)],
            'match_value' => ['required', 'string', 'max:255'],
            'action_type' => ['required', 'string', 'in:' . implode(',', PrintRule::ACTION_TYPES)],
            'printer_id' => ['nullable', 'exists:printers,id', 'required_if:action_type,print_and_send,print_only'],
        ]);

        PrintRule::create($validated);

        return redirect()->route('print-rules.index')->with('success', 'تمت إضافة القاعدة بنجاح');
    }

    public function update(Request $request, PrintRule $printRule)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'min:0'],
            'match_type' => ['required', 'string', 'in:' . implode(',', PrintRule::MATCH_TYPES)],
            'match_value' => ['required', 'string', 'max:255'],
            'action_type' => ['required', 'string', 'in:' . implode(',', PrintRule::ACTION_TYPES)],
            'printer_id' => ['nullable', 'exists:printers,id', 'required_if:action_type,print_and_send,print_only'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $printRule->update($validated);

        return redirect()->route('print-rules.index')->with('success', 'تم تحديث القاعدة بنجاح');
    }

    public function destroy(PrintRule $printRule)
    {
        $printRule->delete();

        return redirect()->route('print-rules.index')->with('success', 'تم حذف القاعدة بنجاح');
    }
}
