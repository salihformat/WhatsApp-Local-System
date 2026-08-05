<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('محرك الأتمتة العام') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach ($errors->all() as $error)<li>- {{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm">
                عند وصول رسالة جديدة، يُفحص شرط كل قاعدة نشطة بالترتيب (الأولوية الأصغر أولاً)، وأول قاعدة مطابقة فقط تُنفَّذ إجراؤها.
            </div>

            <!-- إضافة قاعدة -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-indigo-700">إضافة قاعدة أتمتة</h3>
                <form action="{{ route('automation-rules.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم القاعدة</label>
                        <input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm" placeholder="تعيين شكاوى VIP">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الأولوية</label>
                        <input type="number" name="priority" value="100" min="0" required class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div></div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نوع الشرط</label>
                        <select name="match_type" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="phone_number">رقم جوال محدد</option>
                            <option value="phone_prefix">بادئة رقم الجوال</option>
                            <option value="keyword">كلمة مفتاحية (افصل بفاصلة لأكثر من كلمة)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">قيمة الشرط</label>
                        <input type="text" name="match_value" required class="w-full rounded-md border-gray-300 shadow-sm" placeholder="شكوى,مشكلة,استفسار">
                    </div>
                    <div></div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نوع الإجراء</label>
                        <select name="action_type" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="assign_user">تعيين المحادثة لموظف</option>
                            <option value="internal_note">إضافة ملاحظة داخلية</option>
                            <option value="auto_reply">رد تلقائي فوري</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">قيمة الإجراء</label>
                        <select name="action_value_user" class="w-full rounded-md border-gray-300 shadow-sm mb-2" onchange="document.getElementById('action_value_text').value = this.value">
                            <option value="">— اختر موظفاً (لإجراء «تعيين لموظف» فقط) —</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="action_value_text" name="action_value" required class="w-full rounded-md border-gray-300 shadow-sm" placeholder="نص الملاحظة أو الرد التلقائي، أو معرّف الموظف أعلاه">
                    </div>

                    <div class="md:col-span-3">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">إضافة القاعدة</button>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-3">لإجراء "تعيين لموظف": اختر الموظف من القائمة (يملأ الحقل تلقائياً بمعرّفه). لباقي الإجراءات: اكتب النص مباشرة.</p>
            </div>

            <!-- قائمة القواعد -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الأولوية</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الشرط</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراء</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مفعّلة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rules as $rule)
                            @php
                                $matchLabels = ['phone_number' => 'رقم =', 'phone_prefix' => 'بادئة =', 'keyword' => 'كلمة تحتوي'];
                                $actionLabels = ['assign_user' => 'تعيين للموظف #', 'internal_note' => 'ملاحظة:', 'auto_reply' => 'رد تلقائي:'];
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $rule->priority }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $rule->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">{{ $matchLabels[$rule->match_type] ?? $rule->match_type }} "{{ $rule->match_value }}"</td>
                                <td class="px-6 py-4 text-gray-500 text-sm max-w-xs truncate" title="{{ $rule->action_value }}">{{ $actionLabels[$rule->action_type] ?? $rule->action_type }} {{ $rule->action_value }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('automation-rules.update', $rule) }}" method="POST" class="inline-flex">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $rule->name }}">
                                        <input type="hidden" name="priority" value="{{ $rule->priority }}">
                                        <input type="hidden" name="match_type" value="{{ $rule->match_type }}">
                                        <input type="hidden" name="match_value" value="{{ $rule->match_value }}">
                                        <input type="hidden" name="action_type" value="{{ $rule->action_type }}">
                                        <input type="hidden" name="action_value" value="{{ $rule->action_value }}">
                                        <input type="checkbox" name="is_active" value="1" onchange="this.form.submit()" {{ $rule->is_active ? 'checked' : '' }}>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('automation-rules.destroy', $rule) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذه القاعدة؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">لا توجد قواعد أتمتة بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
