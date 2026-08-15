<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة الطابعات') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('warning') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @unless(config('printing.enabled'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative">
                    ميزة الطباعة الذكية معطّلة حالياً (<code>PRINTING_ENABLED=false</code> في <code>.env</code>). يمكنك إعداد الطابعات والقواعد الآن، لكن لن تُطبع أي ملفات فعلياً حتى تفعيلها.
                </div>
            @endunless

            <!-- إضافة طابعة -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-indigo-700">إضافة طابعة جديدة</h3>
                <form action="{{ route('printers.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم مختصر (للعرض)</label>
                        <input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm" placeholder="طابعة الاستقبال">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم الطابعة في Windows</label>
                        <input type="text" name="windows_printer_name" required class="w-full rounded-md border-gray-300 shadow-sm" placeholder="HP LaserJet Professional P1102">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
                        <select name="type" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="document">مستندات (PDF)</option>
                            <option value="thermal" disabled>حرارية / ملصقات (قريباً)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">وضع الطباعة</label>
                        <select name="print_mode" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="auto">تلقائي (يُطبع فوراً)</option>
                            <option value="approval">يتطلب موافقة</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_default" value="1" id="is_default_new" class="rounded border-gray-300">
                        <label for="is_default_new" class="text-sm text-gray-700" title="تُستخدم هذه الطابعة تلقائياً لطباعة أي ملف قابل للطباعة لا يُطابق أي قاعدة توجيه محددة في صفحة قواعد التوجيه (رقم جوال/كلمة مفتاحية/نوع ملف) — بلا هذا الخيار، أي ملف لا يُطابق قاعدة يبقى بلا طابعة ولا يُطبع إطلاقاً. طابعة واحدة فقط يمكن أن تكون افتراضية.">طابعة افتراضية 🛈</label>
                    </div>
                    <div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 w-full">إضافة</button>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-5">
                        <input type="checkbox" name="supports_status_check" value="1" id="supports_status_check_new" class="rounded border-gray-300">
                        <label for="supports_status_check_new" class="text-sm text-gray-700">
                            هذه الطابعة تُبلّغ فعلياً عن أعطالها (نفاد ورق/حبر) عبر Windows — لا تُفعّلها إلا بعد التأكد يدوياً، وإلا فقد يصل العميل تأكيد طباعة كاذب.
                        </label>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    "يتطلب موافقة": لن يُطبع أي ملف مطابق لهذه الطابعة تلقائياً — يصل طلب لرقم
                    <code>PRINTER_ALERT_PHONE</code> (المسؤول) عبر واتساب، ويوافَق عليه إما بزر من صفحة
                    <a href="{{ route('print-jobs.index') }}" class="text-indigo-600 hover:underline">سجل عمليات الطباعة</a>
                    أو بالرد على واتساب بـ "وافق طباعة &lt;رقم المهمة&gt;" أو "رفض طباعة &lt;رقم المهمة&gt;"
                    (أو "ارسل لي الملف طباعة &lt;رقم المهمة&gt;" لمعاينته أولاً).
                </p>
                <p class="text-xs text-gray-500 mt-2">استخدم بالضبط الاسم كما يظهر في أمر <code>Get-Printer</code> على Windows.</p>
                <p class="text-xs text-gray-500 mt-2">
                    <strong>الطابعة الافتراضية:</strong> هي الطابعة التي تُستخدم تلقائياً لأي ملف قابل للطباعة وصل عبر واتساب ولم يُطابق أي
                    <a href="{{ route('print-rules.index') }}" class="text-indigo-600 hover:underline">قاعدة توجيه</a>
                    محددة (رقم جوال/كلمة مفتاحية/نوع ملف). بدون تعيين طابعة افتراضية، أي ملف لا يُطابق قاعدة صريحة يبقى بلا طباعة إطلاقاً. يمكن تعيين طابعة واحدة فقط كافتراضية في نفس الوقت — تفعيلها لطابعة يُلغيها تلقائياً من أي طابعة أخرى.
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    <strong>تحويل تلقائي عند التعطل:</strong> عيّن لكل طابعة "طابعة احتياطية" من العمود المخصص في الجدول أدناه — إن أظهر آخر فحص دوري (كل 10 دقائق) أن الطابعة الأصلية غير سليمة (نفاد ورق/حبر/غير متصلة)، تُحوَّل مهام الطباعة الجديدة تلقائياً للاحتياطية (بشرط أن تكون هي نفسها سليمة ومفعّلة)، مع تنبيه واتساب للمسؤول بالتحويل. لا يعيد النظام محاولة الطابعة الأصلية تلقائياً بعد عودتها — كل مهمة جديدة تُقيَّم من جديد وقت وصولها.
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    <strong>طباعة محلية مباشرة (بلا واتساب):</strong> ضع أي ملف داخل
                    <code>{{ rtrim(config('app.monitor_folder_path'), '/\\') }}\print\&lt;اسم الطابعة&gt;\</code>
                    وسيُطبع تلقائياً (أو ينتظر موافقة) حسب "وضع الطباعة" أعلاه لتلك الطابعة — المسار الدقيق لكل طابعة موضّح تحت اسمها في الجدول أدناه، ويُنشأ تلقائياً عند أول تشغيل لأمر <code>monitor:folder</code>.
                </p>
            </div>

            <!-- قائمة الطابعات -->
            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">اسم Windows</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وضع الطباعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="الطابعة التي تُستخدم تلقائياً لأي ملف لا يُطابق أي قاعدة توجيه محددة">افتراضية 🛈</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مفعّلة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تأكيد الطباعة للعميل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">حالة الطابعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="عند تعطّل هذه الطابعة (حسب آخر فحص دوري)، تُحوَّل مهام الطباعة الجديدة تلقائياً لهذه الطابعة الاحتياطية">احتياطية عند التعطل 🛈</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد مهام الطباعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="إجمالي الصفحات المطبوعة فعلياً — تقدير تقريبي لتخطيط استهلاك الحبر/الورق">الصفحات المطبوعة 🛈</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($printers as $printer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    {{ $printer->name }}
                                    <div class="text-xs text-gray-400 font-normal" title="مجلد الطباعة المحلية المباشرة لهذه الطابعة">
                                        {{ app(\App\Services\PrintFolderManager::class)->printerFolderPath($printer) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $printer->windows_printer_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $printer->type === 'document' ? 'مستندات' : 'حرارية' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_default" value="{{ $printer->is_default ? 1 : 0 }}">
                                        <input type="hidden" name="is_active" value="{{ $printer->is_active ? 1 : 0 }}">
                                        <input type="hidden" name="supports_status_check" value="{{ $printer->supports_status_check ? 1 : 0 }}">
                                        <select name="print_mode" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 shadow-sm {{ $printer->print_mode === 'approval' ? 'text-orange-700 font-medium' : 'text-gray-600' }}" title="تلقائي: يُطبع فوراً. يتطلب موافقة: يُحجز الطلب حتى تُوافق عليه عبر لوحة التحكم أو رد واتساب.">
                                            <option value="auto" {{ $printer->print_mode === 'auto' ? 'selected' : '' }}>تلقائي</option>
                                            <option value="approval" {{ $printer->print_mode === 'approval' ? 'selected' : '' }}>يتطلب موافقة</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_active" value="{{ $printer->is_active ? 1 : 0 }}">
                                        <input type="hidden" name="supports_status_check" value="{{ $printer->supports_status_check ? 1 : 0 }}">
                                        <input type="checkbox" name="is_default" value="1" onchange="this.form.submit()" {{ $printer->is_default ? 'checked' : '' }} title="تعيين كطابعة افتراضية (تطبع كل PDF وارد لا تطابق أي قاعدة أخرى)">
                                        <span class="text-xs {{ $printer->is_default ? 'text-indigo-700 font-medium' : 'text-gray-400' }}">افتراضية</span>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $printer->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $printer->is_active ? 'مفعّلة' : 'معطّلة' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_default" value="{{ $printer->is_default ? 1 : 0 }}">
                                        <input type="hidden" name="is_active" value="{{ $printer->is_active ? 1 : 0 }}">
                                        <input type="checkbox" name="supports_status_check" value="1" onchange="this.form.submit()" {{ $printer->supports_status_check ? 'checked' : '' }} title="فعّلها فقط بعد تأكيد يدوي أن الطابعة تُبلّغ فعلياً عن نفاد الورق/الحبر عبر Windows">
                                        <span class="text-xs {{ $printer->supports_status_check ? 'text-indigo-700 font-medium' : 'text-gray-400' }}">
                                            {{ $printer->supports_status_check ? 'موثوقة ✓' : 'غير مؤكَّدة' }}
                                        </span>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(!$printer->last_checked_at)
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">لم تُفحص بعد</span>
                                    @else
                                        @php
                                            $statusColors = [
                                                'healthy' => 'bg-green-100 text-green-700',
                                                'offline' => 'bg-red-100 text-red-700',
                                                'error' => 'bg-orange-100 text-orange-700',
                                                'unknown' => 'bg-gray-100 text-gray-500',
                                            ];
                                            $statusLabels = [
                                                'healthy' => 'تعمل بشكل طبيعي',
                                                'offline' => 'غير متصلة',
                                                'error' => 'بها مشكلة',
                                                'unknown' => 'غير معروفة',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$printer->last_status] ?? 'bg-gray-100 text-gray-500' }}" title="{{ $printer->last_status_detail }}">
                                            {{ $statusLabels[$printer->last_status] ?? $printer->last_status }}
                                        </span>
                                        <div class="text-xs text-gray-400 mt-1">{{ $printer->last_checked_at->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_default" value="{{ $printer->is_default ? 1 : 0 }}">
                                        <input type="hidden" name="is_active" value="{{ $printer->is_active ? 1 : 0 }}">
                                        <input type="hidden" name="supports_status_check" value="{{ $printer->supports_status_check ? 1 : 0 }}">
                                        <select name="fallback_printer_id" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 shadow-sm" title="الطابعة التي تُستخدم تلقائياً لأي مهمة طباعة جديدة موجّهة لهذه الطابعة إن كانت غير سليمة حسب آخر فحص دوري">
                                            <option value="">بلا تحويل تلقائي</option>
                                            @foreach($printers as $other)
                                                @if($other->id !== $printer->id)
                                                    <option value="{{ $other->id }}" {{ $printer->fallback_printer_id === $other->id ? 'selected' : '' }}>{{ $other->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $printer->print_jobs_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ number_format($printer->pages_printed) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-2 space-x-reverse">
                                    <form action="{{ route('printers.check-now', $printer) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-indigo-200 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            فحص الآن
                                        </button>
                                    </form>
                                    <form action="{{ route('printers.update', $printer) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="name" value="{{ $printer->name }}">
                                        <input type="hidden" name="windows_printer_name" value="{{ $printer->windows_printer_name }}">
                                        <input type="hidden" name="type" value="{{ $printer->type }}">
                                        <input type="hidden" name="is_default" value="{{ $printer->is_default ? 1 : 0 }}">
                                        <input type="hidden" name="supports_status_check" value="{{ $printer->supports_status_check ? 1 : 0 }}">
                                        <input type="checkbox" name="is_active" value="1" onchange="this.form.submit()" {{ $printer->is_active ? 'checked' : '' }} title="تفعيل/تعطيل">
                                    </form>
                                    <form action="{{ route('printers.destroy', $printer) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('هل أنت متأكد من حذف هذه الطابعة؟ سيتم مسح أي مهام طباعة معلقة لها.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md transition-colors text-xs font-medium" style="background-color: #fee2e2; color: #dc2626;" onmouseover="this.style.backgroundColor='#fecaca'" onmouseout="this.style.backgroundColor='#fee2e2'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            حذف الطابعة
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-6 py-4 text-center text-gray-500">لا توجد طابعات مضافة بعد</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('print-rules.index') }}" class="text-indigo-600 hover:underline">إدارة قواعد التوجيه ←</a>
                <a href="{{ route('print-jobs.index') }}" class="text-indigo-600 hover:underline">سجل عمليات الطباعة ←</a>
            </div>
        </div>
    </div>
</x-app-layout>
