<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $assignableUsers = User::orderBy('name')->get(['id', 'name', 'role', 'is_available_for_assignment']);

        return view('settings.index', compact('assignableUsers'));
    }

    public function update(UpdateSettingRequest $request)
    {
        $validated = $request->validated();

        $this->updateEnvFile($validated);

        // تسجيل أسماء المفاتيح المُعدَّلة فقط في سجل التدقيق، بدون قيمها الفعلية (قد تتضمن
        // توكنات/كلمات مرور) — يكفي معرفة من غيّر أي إعداد ومتى لأغراض المساءلة.
        activity('settings')
            ->causedBy(auth()->user())
            ->withProperties(['changed_keys' => array_keys($validated)])
            ->log('تحديث إعدادات النظام: ' . implode(', ', array_keys($validated)));

        return back()->with('success', 'تم حفظ الإعدادات بنجاح. قد تحتاج إلى إعادة تشغيل النظام لتطبيق بعض الإعدادات.');
    }

    protected function updateEnvFile(array $data)
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            return;
        }

        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            // Avoid null values being written as empty keys, use empty string if null
            $value = $value ?? '';

            // For string values containing spaces, we wrap them in quotes if not already
            if (preg_match('/\s/', $value) && !preg_match('/^".*"$/', $value)) {
                $value = '"' . $value . '"';
            }
            
            // Check if key exists
            if (preg_match('/^' . $key . '=/m', $envContent)) {
                // Replace existing
                $envContent = preg_replace(
                    '/^' . $key . '=.*/m',
                    $key . '=' . $value,
                    $envContent
                );
            } else {
                // Append if not exists
                $envContent .= PHP_EOL . $key . '=' . $value;
            }
        }

        File::put($envPath, $envContent);
    }
}
