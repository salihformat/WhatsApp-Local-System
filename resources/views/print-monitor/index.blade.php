<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('متابعة مجلد المراقبة (PrintMonitor)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded relative">{{ session('info') }}</div>
            @endif

            @php
                $hasReviewFiles = count($folders['review']['files'] ?? []) > 0;
            @endphp
            <div class="flex justify-end">
                <form action="{{ route('print-monitor.approve-all') }}" method="POST" onsubmit="confirmAction(event, 'هل أنت متأكد من الموافقة على كل الملفات المعلّقة حالياً؟', 'موافقة');">
                    @csrf
                    <button type="submit" 
                        class="px-4 py-2 rounded-md transition-colors border text-sm font-medium 
                            {{ $hasReviewFiles ? 'text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100 border-green-200' : 'text-gray-400 bg-gray-100 border-gray-200 cursor-not-allowed opacity-75' }}"
                        {{ !$hasReviewFiles ? 'disabled' : '' }}
                        title="{{ !$hasReviewFiles ? 'لا توجد ملفات بانتظار المراجعة حالياً' : '' }}">
                        موافقة على كل الملفات المعلّقة
                    </button>
                </form>
            </div>

            @unless($folderExists)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    المجلد <code>{{ $folderPath }}</code> غير موجود حالياً — سيُنشأ تلقائياً عند أول تشغيل لأمر <code>monitor:folder</code>.
                </div>
            @endunless

            @if(config('app.monitor_folder_require_approval'))
                <div class="bg-orange-50 border border-orange-300 text-orange-800 px-4 py-3 rounded relative text-sm">
                    ⏸️ "موافقة قبل الإرسال" مفعّلة لكل ملفات هذا المجلد (<code>MONITOR_FOLDER_REQUIRE_APPROVAL=true</code>) — لن يُرسل أي ملف عبر واتساب تلقائياً، بل يصل طلب موافقة لرقم المسؤول ويظهر هنا بقسم "بانتظار المراجعة" أدناه.
                    وافِق عبر الزر أدناه أو برد واتساب "وافق ارسال &lt;رقم الرسالة&gt;" / "رفض ارسال &lt;رقم الرسالة&gt;"
                    (أو "ارسل لي الملف ارسال &lt;رقم الرسالة&gt;" لمعاينته أولاً).
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 text-gray-600 px-4 py-3 rounded relative text-sm">
                    الإرسال التلقائي مفعّل لهذا المجلد — تُرسل الملفات فور استخراج رقم جوال بثقة كافية. لتفعيل موافقة إلزامية على كل ملف قبل إرساله، اضبط <code>MONITOR_FOLDER_REQUIRE_APPROVAL=true</code> في <code>.env</code>.
                </div>
            @endif

            <!-- بطاقات ملخّص -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $cardColors = [
                        'pending' => 'border-gray-400',
                        'review' => 'border-yellow-500',
                        'processing' => 'border-blue-500',
                        'archive' => 'border-green-500',
                        'failed' => 'border-red-500',
                    ];
                @endphp
                @foreach($folders as $key => $folder)
                    <div class="bg-white rounded-lg shadow-sm p-5 border-r-4 {{ $cardColors[$key] }}">
                        <div class="text-xs text-gray-500 mb-1">{{ $folder['label'] }}</div>
                        <div class="text-2xl font-bold text-gray-800">{{ count($folder['files']) }}</div>
                    </div>
                @endforeach
            </div>

            <!-- تفاصيل كل مجلد -->
            @foreach($folders as $key => $folder)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 font-bold text-gray-700">
                        {{ $folder['label'] }} ({{ count($folder['files']) }})
                        @if(count($folder['files']) === 100)
                            <span class="text-xs text-gray-400 font-normal">— آخر 100 فقط</span>
                        @endif
                    </div>
                    @php
                        $colCount = 5; // اسم الملف، الحجم، آخر تعديل، رقم الجوال، تفاصيل البحث
                        if ($key === 'failed' || $key === 'processing') $colCount++;
                        if ($key === 'review') $colCount++;
                    @endphp
                    @if($key === 'review' && count($folder['files']) > 0)
                        <div class="px-6 py-2 bg-yellow-50 border-b border-yellow-200 text-xs text-yellow-800">
                            هذه الملفات إما استُخرج رقم الجوال لها من مصدر منخفض الثقة (بلا تسمية صريحة)، أو تتطلب موافقة عامة، أو (إن ظهر حقل إدخال رقم) تعذّر استخراج أي رقم لها تلقائياً — أدخل الرقم يدوياً في الحقل ثم اضغط "إرسال". راجع "تفاصيل البحث" لمعرفة السبب قبل الموافقة أو الرفض.
                        </div>
                    @endif
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">اسم الملف</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">الحجم</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">آخر تعديل</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">رقم الجوال</th>
                                @if($key === 'failed' || $key === 'processing')
                                    <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">سبب الفشل</th>
                                @endif
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">تفاصيل البحث</th>
                                @if($key === 'review')
                                    <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">الإجراء</th>
                                @endif
                            </tr>
                        </thead>
                        @forelse($folder['files'] as $file)
                            <tbody x-data="{ open: false }" class="bg-white divide-y divide-gray-100">
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-800 break-all">{{ $file['name'] }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ $file['size'] }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::createFromTimestamp($file['modified_at'])->diffForHumans() }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500" dir="ltr">
                                        {{ $file['phone_number'] ?? '—' }}
                                    </td>
                                    @if($key === 'failed' || $key === 'processing')
                                        <td class="px-6 py-3 text-sm text-red-600 break-all">
                                            @if($file['error_message'])
                                                {{ $file['error_message'] }}
                                            @elseif($key === 'failed' && empty($file['phone_number']))
                                                لم يتم العثور على رقم جوال في اسم الملف أو محتواه
                                            @elseif($key === 'failed' && $file['status'] && !in_array($file['status'], ['failed', 'no_whatsapp']))
                                                <span class="text-gray-400">نسخة قديمة من الملف — آخر محاولة إرسال بنفس الاسم نجحت لاحقاً (الحالة الحالية: {{ $file['status'] }}). يمكن حذف هذا الملف بأمان.</span>
                                            @elseif($file['status'] === 'pending' || $file['status'] === 'processing')
                                                <span class="text-gray-400">قيد الإرسال…</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">
                                        @if($file['trace'])
                                            <button type="button" @click="open = !open" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium underline">
                                                <span x-show="!open">عرض التفاصيل</span>
                                                <span x-show="open" style="display:none">إخفاء</span>
                                            </button>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    @if($key === 'review')
                                        <td class="px-6 py-3 whitespace-nowrap text-sm">
                                            @if(!$file['message_id'])
                                                <span class="text-gray-300 text-xs">لم يُعثر على سجل الرسالة</span>
                                            @elseif($file['needs_phone_entry'] ?? false)
                                                <form action="{{ route('print-monitor.set-phone-and-approve', $file['message_id']) }}" method="POST" class="flex gap-2 items-center" onsubmit="return confirmAction(event, 'سيتم إرسال الملف فعلياً إلى هذا الرقم. متابعة؟', 'إرسال');">
                                                    @csrf
                                                    <input type="text" name="phone_number" required placeholder="9665xxxxxxxx" dir="ltr" class="w-32 text-xs rounded-md border-gray-300 shadow-sm" title="لم يتمكن النظام من استخراج رقم جوال تلقائياً — أدخله هنا">
                                                    <button type="submit" class="px-3 py-1 rounded-md bg-green-600 text-white text-xs font-medium hover:bg-green-700">إرسال</button>
                                                </form>
                                                <form action="{{ route('print-monitor.reject', $file['message_id']) }}" method="POST" class="inline mt-1" onsubmit="return confirmAction(event, 'سيتم رفض هذا الملف ونقله لمجلد فشلت بدون إرسال. متابعة؟', 'رفض', '#dc2626');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 border border-red-200">رفض</button>
                                                </form>
                                            @else
                                                <div class="flex gap-2">
                                                    <form action="{{ route('print-monitor.approve', $file['message_id']) }}" method="POST" onsubmit="confirmAction(event, 'سيتم إرسال الملف فعلياً إلى {{ $file['phone_number'] }}. متابعة؟', 'موافقة');">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 rounded-md bg-green-600 text-white text-xs font-medium hover:bg-green-700">موافقة وإرسال</button>
                                                    </form>
                                                    <form action="{{ route('print-monitor.reject', $file['message_id']) }}" method="POST" onsubmit="confirmAction(event, 'سيتم رفض هذا الملف ونقله لمجلد فشلت بدون إرسال. متابعة؟', 'رفض', '#dc2626');">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 rounded-md bg-red-600 text-white text-xs font-medium hover:bg-red-700">رفض</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @if($file['trace'])
                                    <tr x-show="open" style="display:none">
                                        <td colspan="{{ $colCount }}" class="px-6 py-3 bg-indigo-50/50 text-xs text-gray-700">
                                            <div class="space-y-1">
                                                <div><span class="font-semibold">آلية الاستخراج:</span> {{ $file['trace']['source_label'] }}</div>
                                                @if($file['trace']['learned_trusted'])
                                                    <div class="text-green-700">✅ تم تخطي المراجعة اليدوية تلقائياً — رقم موثوق بالتعلّم من موافقات سابقة على نفس الرقم من نفس المصدر.</div>
                                                @endif
                                                @if($file['trace']['rtl_corrected'])
                                                    <div class="text-amber-700">⚠ تم تصحيح انعكاس ترتيب الأحرف العربية في النص قبل المطابقة (مشكلة معروفة في استخراج بعض ملفات PDF).</div>
                                                @endif
                                                @if($file['trace']['pdf_ocr_used'])
                                                    <div class="text-purple-700">🖼️ طبقة نص الملف كانت تالفة/غير موجودة — تم تحويل الصفحة الأولى لصورة وقراءتها عبر OCR للحصول على النتيجة.</div>
                                                @endif
                                                @if($file['trace']['matched_label'])
                                                    <div><span class="font-semibold">الكلمة المطابقة:</span> <code dir="ltr">{{ $file['trace']['matched_label'] }}</code></div>
                                                @endif
                                                @if($file['trace']['file_number'])
                                                    <div>
                                                        <span class="font-semibold">رقم الملف المستخرج:</span> {{ $file['trace']['file_number'] }}
                                                        —
                                                        @if($file['trace']['contact_found'])
                                                            <span class="text-green-700">تم العثور على جهة اتصال مطابقة ✓</span>
                                                        @else
                                                            <span class="text-red-600">لا توجد جهة اتصال بهذا الرقم ✗</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if(!empty($file['trace']['excluded']))
                                                    <div>
                                                        <span class="font-semibold">أرقام تم تجاهلها أثناء البحث:</span>
                                                        <ul class="list-disc pr-5 mt-1 space-y-0.5">
                                                            @foreach($file['trace']['excluded'] as $ex)
                                                                <li dir="ltr" class="text-right" dir="rtl">
                                                                    <span dir="ltr">{{ $ex['value'] }}</span>
                                                                    — طابق كلمة "<span dir="ltr">{{ $ex['matched_label'] }}</span>"
                                                                    لكن استُبعد بسبب وجود كلمة "<span dir="ltr">{{ $ex['excluded_by'] }}</span>" قريباً منه
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        @empty
                            <tbody>
                                <tr><td colspan="{{ $colCount }}" class="px-6 py-4 text-center text-gray-400 text-sm">لا توجد ملفات</td></tr>
                            </tbody>
                        @endforelse
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmAction(event, message, confirmText = 'موافق', confirmColor = '#16a34a') {
        event.preventDefault();
        let form = event.target;
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endpush
