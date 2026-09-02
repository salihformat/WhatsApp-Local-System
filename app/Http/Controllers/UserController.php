<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['index', 'show']);
    }

    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,supervisor,agent'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $this->normalizePhoneNumber($validated['phone_number'] ?? null),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_admin' => $validated['role'] === 'admin',
        ]);

        return redirect()->route('users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,supervisor,agent'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $this->normalizePhoneNumber($validated['phone_number'] ?? null);

        $passwordChanged = !empty($validated['password']);
        if ($passwordChanged) {
            $user->password = Hash::make($validated['password']);
        }

        $user->role = $validated['role'];
        $user->is_admin = $validated['role'] === 'admin';

        if ($user->id !== auth()->id()) {
            $newStatus = $request->has('is_active');
            
            if (!$newStatus && $user->isAdmin() && $user->is_active) {
                $activeAdminCount = User::where('is_admin', true)->where('is_active', true)->count();
                if ($activeAdminCount <= 1) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'لا يمكنك تعطيل آخر حساب مدير نظام. يجب أن يبقى مدير واحد نشط على الأقل.');
                }
            }
            $user->is_active = $newStatus;
        }

        $user->save();

        // كلمة المرور مستثناة عمداً من التدقيق التلقائي للحقول (لا نُخزّن قيمتها ولو مُجزّأة)،
        // لذا نُسجّل هنا فقط أن التغيير حدث، بمعزل عن تدقيق باقي الحقول (name/email/role) التلقائي.
        if ($passwordChanged) {
            activity('users')
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log("تم تغيير كلمة مرور المستخدم: {$user->name}");
        }

        return redirect()->route('users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * تبديل حالة "متاح لاستلام محادثات جديدة" لهذا المستخدم — يستثنيه فوراً من التوزيع التلقائي
     * (راجع ConversationDistributionService) بلا حاجة لحذفه من قائمة "مستخدمين محددين" بالإعدادات
     * أو تعديل دوره، مفيد لإجازة/انشغال مؤقت.
     */
    public function toggleAvailability(User $user)
    {
        $user->update(['is_available_for_assignment' => !$user->is_available_for_assignment]);

        activity('users')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log($user->is_available_for_assignment
                ? "تم تفعيل استلام المحادثات التلقائي للمستخدم: {$user->name}"
                : "تم إيقاف استلام المحادثات التلقائي للمستخدم: {$user->name}");

        return redirect()->back()->with('success', $user->is_available_for_assignment
            ? "{$user->name} أصبح متاحاً لاستلام محادثات جديدة."
            : "{$user->name} لن يستلم محادثات جديدة تلقائياً حتى تُفعّله مجدداً.");
    }

    /**
     * تفعيل أو تعطيل حساب المستخدم — المستخدم المُعطَّل لا يستطيع تسجيل الدخول
     * ويُستثنى فوراً من التوزيع التلقائي بمعزل عن إعداد is_available_for_assignment.
     */
    public function toggleStatus(User $user)
    {
        // منع تعطيل الحساب الشخصي
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'لا يمكنك تعطيل حسابك الشخصي.');
        }

        // منع تعطيل آخر حساب Admin لتجنب قفل النظام
        if ($user->isAdmin() && $user->is_active) {
            $activeAdminCount = User::where('is_admin', true)->where('is_active', true)->count();
            if ($activeAdminCount <= 1) {
                return redirect()->back()
                    ->with('error', 'لا يمكنك تعطيل آخر حساب مدير نظام. يجب أن يبقى مدير واحد نشط على الأقل.');
            }
        }

        $user->update(['is_active' => !$user->is_active]);

        activity('users')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log($user->is_active
                ? "تم تفعيل حساب المستخدم: {$user->name}"
                : "تم تعطيل حساب المستخدم: {$user->name}");

        return redirect()->back()->with('success', $user->is_active
            ? "تم تفعيل حساب {$user->name} بنجاح."
            : "تم تعطيل حساب {$user->name}. لن يتمكن من تسجيل الدخول حتى تُعيد تفعيله.");
    }

    /**
     * توحيد صيغة رقم واتساب الموظف إلى الصيغة الدولية (966...) — نفس منطق التطبيع المستخدم في
     * باقي النظام (MessageController/AdminNotifier)، حتى يُقارَن بصيغة موحّدة عند الإرسال لاحقاً.
     */
    private function normalizePhoneNumber(?string $phoneNumber): ?string
    {
        if (empty($phoneNumber)) {
            return null;
        }

        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '966' . substr($phoneNumber, 1);
        } elseif (strlen($phoneNumber) === 9) {
            $phoneNumber = '966' . $phoneNumber;
        }

        return $phoneNumber ?: null;
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'لا يمكنك حذف حسابك الشخصي');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }
}
