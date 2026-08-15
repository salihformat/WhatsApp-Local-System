<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $assignableUsers = User::orderBy('name')->get(['id', 'name', 'role', 'is_available_for_assignment']);

        // تحميل كل الإعدادات من DB (عبر الكاش) لعرضها في الواجهة
        $settings = Setting::getAllCached();

        return view('settings.index', compact('assignableUsers', 'settings'));
    }

    public function update(UpdateSettingRequest $request)
    {
        $validated = $request->validated();

        // حفظ كل الإعدادات في جدول settings ومسح الكاش دفعة واحدة
        Setting::setMany($validated);

        // تسجيل أسماء المفاتيح المُعدَّلة فقط في سجل التدقيق، بدون قيمها الفعلية (قد تتضمن
        // توكنات/كلمات مرور) — يكفي معرفة من غيّر أي إعداد ومتى لأغراض المساءلة.
        activity('settings')
            ->causedBy(auth()->user())
            ->withProperties(['changed_keys' => array_keys($validated)])
            ->log('تحديث إعدادات النظام: ' . implode(', ', array_keys($validated)));

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
