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
                        <label class="block text-sm font-bold text-gray-700 mb-2">القيمة <span class="text-xs text-gray-400 font-normal">(يمكن وضع أكثر من قيمة مفصولة بفاصلة، لأي نوع مطابقة)</span></label>
                        <input type="text" name="match_value" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="مثال: طباعة,اطبع,print — أو أرقام: 966501111111,966502222222">
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
                                    <div class="flex items-center justify-center gap-2" x-data="{ editModalOpen: false }">
                                        <!-- Edit Button -->
                                        <button @click="editModalOpen = true" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition-colors flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            تعديل
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('print-rules.destroy', $rule) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه القاعدة؟');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                حذف
                                            </button>
                                        </form>

                                        <!-- Edit Modal -->
                                        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="editModalOpen = false" aria-hidden="true"></div>

                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.stop>
                                                    <form action="{{ route('print-rules.update', $rule) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-right whitespace-normal text-base">
                                                            <h3 class="text-lg leading-6 font-bold text-indigo-700 mb-6 border-b pb-3" id="modal-title">
                                                                تعديل قاعدة: {{ $rule->name }}
                                                            </h3>
                                                            <div class="space-y-4 text-right">
                                                                <div>
                                                                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم القاعدة</label>
                                                                    <input type="text" name="name" value="{{ $rule->name }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع المطابقة</label>
                                                                    <select name="match_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                        <option value="phone_number" {{ $rule->match_type == 'phone_number' ? 'selected' : '' }}>رقم جوال محدد</option>
                                                                        <option value="phone_prefix" {{ $rule->match_type == 'phone_prefix' ? 'selected' : '' }}>بادئة رقم الجوال</option>
                                                                        <option value="keyword" {{ $rule->match_type == 'keyword' ? 'selected' : '' }}>كلمة مفتاحية</option>
                                                                        <option value="file_type" {{ $rule->match_type == 'file_type' ? 'selected' : '' }}>امتداد الملف</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-bold text-gray-700 mb-2">القيمة <span class="text-xs text-gray-400 font-normal">(يمكن وضع أكثر من قيمة مفصولة بفاصلة)</span></label>
                                                                    <input type="text" name="match_value" value="{{ $rule->match_value }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                </div>

                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <div>
                                                                        <label class="block text-sm font-bold text-gray-700 mb-2">الأولوية</label>
                                                                        <input type="number" name="priority" value="{{ $rule->priority }}" min="0" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                    </div>

                                                                    <div>
                                                                        <label class="block text-sm font-bold text-gray-700 mb-2">الطابعة</label>
                                                                        <select name="printer_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                            @foreach($printers as $printer)
                                                                                <option value="{{ $printer->id }}" {{ $rule->printer_id == $printer->id ? 'selected' : '' }}>{{ $printer->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                
                                                                @if($rule->is_active)
                                                                    <input type="hidden" name="is_active" value="1">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:justify-start gap-2 text-right">
                                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                                                                حفظ التعديلات
                                                            </button>
                                                            <button type="button" @click="editModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 sm:mt-0 sm:w-auto sm:text-sm">
                                                                إلغاء
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
