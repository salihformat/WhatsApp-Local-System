<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('قواعد توجيه الطباعة') }}
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

            @if($printers->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative">
                    يجب <a href="{{ route('printers.index') }}" class="underline font-bold">إضافة طابعة</a> واحدة على الأقل قبل إنشاء قواعد التوجيه.
                </div>
            @endif

            <!-- إضافة قاعدة -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold mb-6 text-indigo-700">إضافة قاعدة توجيه</h3>
                <form action="{{ route('print-rules.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-6 items-end">
                    @csrf
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم القاعدة</label>
                        <input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="مثال: فواتير المحاسبة">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">نوع المطابقة</label>
                        <select name="match_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="phone_number">رقم جوال محدد</option>
                            <option value="phone_prefix">بادئة رقم الجوال</option>
                            <option value="keyword">كلمة مفتاحية</option>
                            <option value="file_type">امتداد الملف</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">القيمة <span class="text-xs text-gray-400 font-normal">(أو كلمات مفصولة بفاصلة)</span></label>
                        <input type="text" name="match_value" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="مثال: طباعة,اطبع,print">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">الأولوية <span class="text-xs text-gray-400 font-normal">(الأصغر يُفحص أولاً)</span></label>
                        <input type="number" name="priority" value="100" min="0" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">الطابعة</label>
                        <select name="printer_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($printers as $printer)
                                <option value="{{ $printer->id }}">{{ $printer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-[10px] rounded-md hover:bg-indigo-700 font-bold shadow-sm transition-colors flex items-center justify-center gap-2 w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            إضافة القاعدة
                        </button>
                    </div>
                </form>
            </div>

            <!-- قائمة القواعد -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">الأولوية</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">الاسم</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">الشرط</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">الطابعة</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">مفعّلة</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">{{ $rule->priority }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-900">{{ $rule->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-600">
                                    @php
                                        $labels = ['phone_number' => 'رقم =', 'phone_prefix' => 'بادئة =', 'keyword' => 'كلمة تحتوي', 'file_type' => 'امتداد ='];
                                    @endphp
                                    {{ $labels[$rule->match_type] ?? $rule->match_type }} "<span class="font-medium text-gray-800">{{ $rule->match_value }}</span>"
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-medium text-gray-800">{{ $rule->printer?->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <form action="{{ route('print-rules.update', $rule) }}" method="POST" class="inline-flex">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $rule->name }}">
                                        <input type="hidden" name="priority" value="{{ $rule->priority }}">
                                        <input type="hidden" name="match_type" value="{{ $rule->match_type }}">
                                        <input type="hidden" name="match_value" value="{{ $rule->match_value }}">
                                        <input type="hidden" name="printer_id" value="{{ $rule->printer_id }}">
                                        <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer" onchange="this.form.submit()" {{ $rule->is_active ? 'checked' : '' }}>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <form action="{{ route('print-rules.destroy', $rule) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه القاعدة؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium transition-colors">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">لا توجد قواعد بعد — أي رسالة PDF واردة ستُرسل للطابعة الافتراضية فقط (إن وُجدت)</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('printers.index') }}" class="text-indigo-600 hover:underline">→ إدارة الطابعات</a>
                <a href="{{ route('print-jobs.index') }}" class="text-indigo-600 hover:underline">سجل عمليات الطباعة ←</a>
            </div>
        </div>
    </div>
</x-app-layout>
