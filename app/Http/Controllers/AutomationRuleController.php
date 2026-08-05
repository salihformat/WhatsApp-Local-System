<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use App\Models\User;
use Illuminate\Http\Request;

class AutomationRuleController extends Controller
{
    public function index()
    {
        $rules = AutomationRule::orderedByPriority()->get();
        $users = User::select('id', 'name')->orderBy('name')->get();

        return view('automation-rules.index', compact('rules', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRule($request);
        AutomationRule::create($validated);

        return redirect()->route('automation-rules.index')->with('success', 'تمت إضافة القاعدة بنجاح');
    }

    public function update(Request $request, AutomationRule $automationRule)
    {
        $validated = $this->validateRule($request);
        $validated['is_active'] = $request->boolean('is_active');
        $automationRule->update($validated);

        return redirect()->route('automation-rules.index')->with('success', 'تم تحديث القاعدة بنجاح');
    }

    public function destroy(AutomationRule $automationRule)
    {
        $automationRule->delete();

        return redirect()->route('automation-rules.index')->with('success', 'تم حذف القاعدة بنجاح');
    }

    private function validateRule(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'min:0'],
            'match_type' => ['required', 'string', 'in:' . implode(',', AutomationRule::MATCH_TYPES)],
            'match_value' => ['required', 'string', 'max:255'],
            'action_type' => ['required', 'string', 'in:' . implode(',', AutomationRule::ACTION_TYPES)],
            'action_value' => ['required', 'string', 'max:2000'],
        ]);
    }
}
