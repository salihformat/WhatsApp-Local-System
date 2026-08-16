<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إعدادات النظام (System Settings)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            <!-- System Info Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">معلومات النظام والجهاز (System Info)</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم النظام المحلي (LOCAL_SYSTEM_NAME)</label>
                                    <input type="text" name="LOCAL_SYSTEM_NAME" value="{{ old('LOCAL_SYSTEM_NAME', $settings['LOCAL_SYSTEM_NAME'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="فرع الرياض">
                                    <p class="text-xs text-gray-400 mt-1">الاسم المميز لهذا النظام المحلي (يظهر في التنبيهات والتقارير).</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم الجهاز (DEVICE_NAME)</label>
                                    <input type="text" name="DEVICE_NAME" value="{{ old('DEVICE_NAME', $settings['DEVICE_NAME'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الموقع (LOCATION)</label>
                                    <input type="text" name="LOCATION" value="{{ old('LOCATION', $settings['LOCATION'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع الباقة (PLAN_TYPE)</label>
                                    <input type="text" name="PLAN_TYPE" value="{{ old('PLAN_TYPE', $settings['PLAN_TYPE'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            
                            <!-- Central API Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">الواجهة المركزية (Central API)</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">معرف الشركة (CENTRAL_API_COMPANY_ID)</label>
                                    <input type="text" name="CENTRAL_API_COMPANY_ID" value="{{ old('CENTRAL_API_COMPANY_ID', $settings['CENTRAL_API_COMPANY_ID'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">رمز المصادقة الصادر (CENTRAL_API_TOKEN)</label>
                                    <input type="text" name="CENTRAL_API_TOKEN" value="{{ old('CENTRAL_API_TOKEN', $settings['CENTRAL_API_TOKEN'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">يستخدمه هذا النظام كـ Bearer عند استدعاء API النظام المركزي (إرسال الرسائل، مزامنة الحالة). يطابق "التوكن السري" الظاهر في صفحة الشركة بالنظام المركزي.</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">رمز التحقق من الويب هوك الوارد (CENTRAL_WEBHOOK_TOKEN)</label>
                                    <input type="text" name="CENTRAL_WEBHOOK_TOKEN" value="{{ old('CENTRAL_WEBHOOK_TOKEN', $settings['CENTRAL_WEBHOOK_TOKEN'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="اتركه فارغاً لاستخدام CENTRAL_API_TOKEN مؤقتاً (غير مُوصى به)">
                                    <p class="text-xs text-gray-500 mt-1">
                                        <strong>أفضل ممارسة:</strong> توكن مستقل تماماً عن الأعلى — يتحقق به هذا النظام أن أي طلب ويب هوك واصل فعلاً من النظام المركزي، وليس نفس التوكن المستخدم للاتصال الصادر. يجب أن يطابق قيمة "التوكن" الخاصة بنقطة الويب هوك المرتبطة بهذا النظام تحديداً في صفحة "الأنظمة الخارجية المرتبطة" بالنظام المركزي (وليس التوكن السري العام للشركة). اتركه فارغاً فقط للتوافق المؤقت مع الإعداد القديم.
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">محاولات إعادة الاتصال (CENTRAL_API_RETRY_ATTEMPTS)</label>
                                    <input type="number" name="CENTRAL_API_RETRY_ATTEMPTS" value="{{ old('CENTRAL_API_RETRY_ATTEMPTS', $settings['CENTRAL_API_RETRY_ATTEMPTS'] ?? '3') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">تأخير إعادة الاتصال بالثواني (CENTRAL_API_RETRY_DELAY)</label>
                                    <input type="number" name="CENTRAL_API_RETRY_DELAY" value="{{ old('CENTRAL_API_RETRY_DELAY', $settings['CENTRAL_API_RETRY_DELAY'] ?? '5') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Local Retry Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">إعادة المحاولة (Local Retry)</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الحد الأقصى لمحاولات إعادة الإرسال (MAX_RETRY_ATTEMPTS)</label>
                                    <input type="number" name="MAX_RETRY_ATTEMPTS" value="{{ old('MAX_RETRY_ATTEMPTS', $settings['MAX_RETRY_ATTEMPTS'] ?? '3') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">تأخير إعادة الإرسال بالدقائق (RETRY_DELAY_MINUTES)</label>
                                    <input type="number" name="RETRY_DELAY_MINUTES" value="{{ old('RETRY_DELAY_MINUTES', $settings['RETRY_DELAY_MINUTES'] ?? '5') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Alert & Monitoring Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">التنبيهات والمراقبة (Alerts & Monitoring)</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم جوال المسؤول (PRINTER_ALERT_PHONE)</label>
                                    <input type="text" name="PRINTER_ALERT_PHONE" value="{{ old('PRINTER_ALERT_PHONE', $settings['PRINTER_ALERT_PHONE'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="966500000000" dir="ltr">
                                    <p class="text-xs text-gray-400 mt-1">رقم (أو أرقام مفصولة بفواصل) جوال المسؤول الذي تصله كل التنبيهات: تعطل الطابعات، طلبات الموافقة، وتنبيهات صحة النظام. اتركه فارغاً لتعطيل التنبيهات.</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">نص الرسالة الافتراضي (MONITORING_MESSAGE_TEXT)</label>
                                    <textarea name="MONITORING_MESSAGE_TEXT" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('MONITORING_MESSAGE_TEXT', $settings['MONITORING_MESSAGE_TEXT'] ?? '') }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">طلب موافقة قبل الإرسال (MONITOR_FOLDER_REQUIRE_APPROVAL)</label>
                                    @php $requireApproval = old('MONITOR_FOLDER_REQUIRE_APPROVAL', $settings['MONITOR_FOLDER_REQUIRE_APPROVAL'] ?? 'false'); @endphp
                                    <select name="MONITOR_FOLDER_REQUIRE_APPROVAL" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="false" @selected($requireApproval === 'false')>معطّل (إرسال تلقائي فوري — الوضع المعتاد)</option>
                                        <option value="true" @selected($requireApproval === 'true')>مفعّل (حجز كل ملف بانتظار موافقة يدوية)</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">عند التفعيل، كل ملف يصل عبر مجلد المراقبة يُحجز بانتظار موافقة صريحة قبل إرساله عبر واتساب.</p>
                                </div>
                            </div>

                            <!-- Local Monitoring Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">الطباعة الذكية (Smart Printing)</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">تفعيل الطباعة الذكية (PRINTING_ENABLED)</label>
                                        @php $printingEnabled = old('PRINTING_ENABLED', $settings['PRINTING_ENABLED'] ?? 'true'); @endphp
                                        <select name="PRINTING_ENABLED" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($printingEnabled === 'true')>مفعّل</option>
                                            <option value="false" @selected($printingEnabled === 'false')>معطّل</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">مقاس الورق (PRINT_IMAGE_PAGE_SIZE)</label>
                                        @php $pageSize = old('PRINT_IMAGE_PAGE_SIZE', $settings['PRINT_IMAGE_PAGE_SIZE'] ?? 'a4'); @endphp
                                        <select name="PRINT_IMAGE_PAGE_SIZE" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="a4" @selected($pageSize === 'a4')>A4</option>
                                            <option value="letter" @selected($pageSize === 'letter')>Letter</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">يجب أن يطابق الورق المُحمّل فعلياً في الطابعة.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">دقة الطباعة DPI (PRINT_IMAGE_DPI)</label>
                                        <input type="number" name="PRINT_IMAGE_DPI" value="{{ old('PRINT_IMAGE_DPI', $settings['PRINT_IMAGE_DPI'] ?? '200') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="72" max="600">
                                        <p class="text-xs text-gray-400 mt-1">200 قيمة متوازنة بين الوضوح وحجم الملف.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">تذكير بطلب الموافقة بعد (دقيقة)</label>
                                        <input type="number" name="PRINTING_APPROVAL_REMINDER_AFTER_MINUTES" value="{{ old('PRINTING_APPROVAL_REMINDER_AFTER_MINUTES', $settings['PRINTING_APPROVAL_REMINDER_AFTER_MINUTES'] ?? '20') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                                        <p class="text-xs text-gray-400 mt-1">بعد كم دقيقة من بقاء طلب موافقة بلا رد يُرسَل تذكير تلقائي للمسؤول.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">تكرار التذكير كل (دقيقة)</label>
                                        <input type="number" name="PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES" value="{{ old('PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES', $settings['PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES'] ?? '30') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                                        <p class="text-xs text-gray-400 mt-1">اضبطه على 0 لتعطيل التذكير التلقائي كلياً.</p>
                                    </div>

                                    <div class="mb-4 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الامتدادات القابلة للطباعة (PRINTABLE_EXTENSIONS) <span class="text-xs text-gray-500">مفصولة بفواصل</span></label>
                                        <input type="text" name="PRINTABLE_EXTENSIONS" value="{{ old('PRINTABLE_EXTENSIONS', $settings['PRINTABLE_EXTENSIONS'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" dir="ltr" placeholder="pdf,jpg,jpeg,png,...">
                                    </div>
                                </div>
                            </div>

                            <!-- Smart Printing Status Replies -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">إشعارات حالة الطباعة الذكية</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">رد فوري عند استلام الطلب (PRINTING_REPLY_ACK_ON_RECEIPT)</label>
                                        @php $replyAck = old('PRINTING_REPLY_ACK_ON_RECEIPT', $settings['PRINTING_REPLY_ACK_ON_RECEIPT'] ?? 'true'); @endphp
                                        <select name="PRINTING_REPLY_ACK_ON_RECEIPT" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($replyAck === 'true')>مفعّل (\"📥 تم استلام طلبك وجاري تنفيذه\" فور تسجيل الطلب)</option>
                                            <option value="false" @selected($replyAck === 'false')>معطّل</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">رسالة منفصلة عن رد النتيجة النهائية — لطمأنة العميل أن ملفه وصل بشكل صحيح.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الرد على من طلب الطباعة (PRINTING_REPLY_STATUS_TO_SENDER)</label>
                                        @php $replySender = old('PRINTING_REPLY_STATUS_TO_SENDER', $settings['PRINTING_REPLY_STATUS_TO_SENDER'] ?? 'true'); @endphp
                                        <select name="PRINTING_REPLY_STATUS_TO_SENDER" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($replySender === 'true')>مفعّل (يصل العميل رسالة تلقائية بنجاح/فشل طباعة ملفه)</option>
                                            <option value="false" @selected($replySender === 'false')>معطّل</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">لا يُرسَل عند كل محاولة فاشلة، فقط عند النجاح أو الفشل النهائي بعد استنفاد كل المحاولات.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">تنبيه فني لصاحب المنشأة عند الفشل (PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE)</label>
                                        @php $notifyOwner = old('PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE', $settings['PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE'] ?? 'true'); @endphp
                                        <select name="PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($notifyOwner === 'true')>مفعّل (يصل الخطأ التقني الكامل لرقم المسؤول)</option>
                                            <option value="false" @selected($notifyOwner === 'false')>معطّل</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- File Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">إعدادات الملفات (File Settings)</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">مسار التخزين (FILE_STORAGE_PATH)</label>
                                        <input type="text" name="FILE_STORAGE_PATH" value="{{ old('FILE_STORAGE_PATH', $settings['FILE_STORAGE_PATH'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">أقصى حجم بالميغابايت (FILE_MAX_SIZE_MB)</label>
                                        <input type="number" name="FILE_MAX_SIZE_MB" value="{{ old('FILE_MAX_SIZE_MB', $settings['FILE_MAX_SIZE_MB'] ?? '20') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الحذف التلقائي بعد (أيام) (FILE_AUTO_DELETE_DAYS)</label>
                                        <input type="number" name="FILE_AUTO_DELETE_DAYS" value="{{ old('FILE_AUTO_DELETE_DAYS', $settings['FILE_AUTO_DELETE_DAYS'] ?? '3') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الاحتفاظ بالنسخ الاحتياطية (أيام) (BACKUP_RETENTION_DAYS)</label>
                                        <input type="number" name="BACKUP_RETENTION_DAYS" value="{{ old('BACKUP_RETENTION_DAYS', $settings['BACKUP_RETENTION_DAYS'] ?? '14') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-400 mt-1">عدد الأيام للاحتفاظ بالنسخ الاحتياطية التلقائية قبل حذفها.</p>
                                    </div>
                                    
                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الأنواع المسموحة (FILE_ALLOWED_TYPES) <span class="text-xs text-gray-500">مفصول بفاصلة</span></label>
                                        <input type="text" name="FILE_ALLOWED_TYPES" value="{{ old('FILE_ALLOWED_TYPES', $settings['FILE_ALLOWED_TYPES'] ?? '') }}" placeholder="pdf,jpg,png" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" dir="ltr">
                                    </div>
                                </div>
                            </div>

                            <!-- PDF/DOCX Phone Extraction Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">استخراج رقم الجوال من محتوى الملفات (PDF/DOCX)</h3>
                                <p class="text-xs text-gray-500 mb-4">تُستخدم هذه الإعدادات عندما لا يحتوي اسم الملف على رقم جوال، فيبحث النظام داخل محتوى الملف نفسه.</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">كود الدولة الافتراضي (DEFAULT_COUNTRY_CODE) <span class="text-xs text-gray-500">للاستخراج الذكي</span></label>
                                        <input type="text" name="DEFAULT_COUNTRY_CODE" value="{{ old('DEFAULT_COUNTRY_CODE', $settings['DEFAULT_COUNTRY_CODE'] ?? '966') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="مثال: 966 أو 20">
                                        <p class="text-xs text-gray-400 mt-1">يُضاف تلقائياً إذا تم استخراج رقم بدون مفتاح أو يبدأ بصفر (يقوم النظام بحذف الصفر وإضافة هذا الكود).</p>
                                    </div>
                                    
                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">آلية استخراج الرقم (PRINT_EXTRACTION_METHOD)</label>
                                        @php $extractionMethod = old('PRINT_EXTRACTION_METHOD', $settings['PRINT_EXTRACTION_METHOD'] ?? 'ocr'); @endphp
                                        <select name="PRINT_EXTRACTION_METHOD" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="ocr" @selected($extractionMethod === 'ocr')>قراءة الرقم آلياً من محتوى الملف (الافتراضي الذكي)</option>
                                            <option value="popup" @selected($extractionMethod === 'popup')>إدخال يدوي من الموقع عند فشل الاستخراج التلقائي</option>
                                            <option value="filename" @selected($extractionMethod === 'filename')>استخراج من اسم الملف فقط (لا يبحث داخل الملف)</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">
                                            في كل الأوضاع الثلاثة يُحاوَل استخراج الرقم أولاً من اسم الملف، ثم من محتواه (إلا في وضع "اسم الملف فقط"). الفرق فقط عند فشل كل محاولات الاستخراج: "الافتراضي الذكي"/"اسم الملف فقط" ينقلان الملف مباشرة لمجلد "فشلت"، بينما "إدخال يدوي من الموقع" يحجزه في تبويب "بانتظار المراجعة" بصفحة متابعة الإرسال (<a href="{{ route('print-monitor.index') }}" class="text-indigo-600 hover:underline">/print-monitor</a>) لتُدخل الرقم يدوياً من هناك ثم يُرسَل. لا يوجد نافذة نظام منبثقة فعلية — هذا لا يمكن أن يعمل مطلقاً في مهمة مجدولة بلا جلسة تفاعلية (Session 0)، لذا استُبدل بهذا المسار العملي عبر الويب.
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمات البحث عن رقم الجوال (PHONE_EXTRACTION_LABELS) <span class="text-xs text-gray-500">مفصولة بفاصلة</span></label>
                                        <textarea name="PHONE_EXTRACTION_LABELS" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="رقم الجوال,الجوال,جوال,phone,mobile">{{ old('PHONE_EXTRACTION_LABELS', $settings['PHONE_EXTRACTION_LABELS'] ?? '') }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">إذا كانت المطابقة قريبة من إحدى هذه الكلمات يُعتبر الرقم بعدها رقم جوال العميل.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمات الاستبعاد (PHONE_EXTRACTION_EXCLUDE_CONTEXT) <span class="text-xs text-gray-500">مفصولة بفاصلة</span></label>
                                        <textarea name="PHONE_EXTRACTION_EXCLUDE_CONTEXT" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="المحل,الشركة,مكتبنا,store,shop,company">{{ old('PHONE_EXTRACTION_EXCLUDE_CONTEXT', $settings['PHONE_EXTRACTION_EXCLUDE_CONTEXT'] ?? '') }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">إذا ظهرت إحدى هذه الكلمات قبل رقم الجوال مباشرة (مثل "هاتف المحل") يتم تجاهل هذا الرقم لأنه رقم الجهة المُصدرة وليس العميل.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمات البحث عن رقم الملف (FILE_NUMBER_LABELS) <span class="text-xs text-gray-500">مفصولة بفاصلة</span></label>
                                        <textarea name="FILE_NUMBER_LABELS" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="رقم الملف,الملف رقم,ملف رقم,file no">{{ old('FILE_NUMBER_LABELS', $settings['FILE_NUMBER_LABELS'] ?? '') }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">عند العثور على رقم بجانب إحدى هذه الكلمات يبحث النظام عن جهة اتصال بنفس رقم الملف ويرسل لرقم جوالها.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">نمط المطابقة (PHONE_MATCH_MODE)</label>
                                        @php $matchMode = old('PHONE_MATCH_MODE', $settings['PHONE_MATCH_MODE'] ?? 'partial'); @endphp
                                        <select name="PHONE_MATCH_MODE" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="partial" @selected($matchMode === 'partial')>جزئي (الكلمة كجزء من نص أطول)</option>
                                            <option value="exact" @selected($matchMode === 'exact')>كامل (الكلمة ككلمة مستقلة فقط)</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">"جزئي" يطابق "جوال" حتى داخل "جوالكم"، أما "كامل" فيتطلب الكلمة منفصلة تماماً.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">السماح بالاستخراج بلا تسمية (ENABLE_UNLABELED_PHONE_FALLBACK)</label>
                                        @php $unlabeledEnabled = old('ENABLE_UNLABELED_PHONE_FALLBACK', $settings['ENABLE_UNLABELED_PHONE_FALLBACK'] ?? 'true'); @endphp
                                        <select name="ENABLE_UNLABELED_PHONE_FALLBACK" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($unlabeledEnabled === 'true')>مفعّل (يبحث عن أي رقم يشبه جوال سعودي بلا تسمية كحل أخير)</option>
                                            <option value="false" @selected($unlabeledEnabled === 'false')>معطّل (إن لم توجد تسمية صريحة يُنقل الملف لمجلد "فشلت")</option>
                                        </select>
                                    </div>

                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">مصادر تتطلب مراجعة يدوية قبل الإرسال (PHONE_REVIEW_REQUIRED_SOURCES) <span class="text-xs text-gray-500">مفصولة بفاصلة</span></label>
                                        <input type="text" name="PHONE_REVIEW_REQUIRED_SOURCES" value="{{ old('PHONE_REVIEW_REQUIRED_SOURCES', $settings['PHONE_REVIEW_REQUIRED_SOURCES'] ?? '') }}" placeholder="unlabeled_fallback,corrupted_fallback,env_fallback" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" dir="ltr">
                                        <p class="text-xs text-gray-400 mt-1">
                                            بدلاً من الإرسال التلقائي مباشرة، أي ملف يُستخرج رقمه من أحد هذه المصادر (منخفضة الثقة) يُحجز في تبويب "بانتظار المراجعة" بصفحة متابعة الإرسال حتى تُوافق عليه يدوياً.
                                            القيم الممكنة: <code dir="ltr">filename, label, file_number, unlabeled_fallback, corrupted_fallback, env_fallback</code>.
                                            اتركه فارغاً للإرسال التلقائي دائماً (بدون مراجعة).
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- كشف التكرار والتعلّم من التصحيح اليدوي -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">كشف التكرار والتعلّم من التصحيح اليدوي</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">كشف التكرار (DUPLICATE_DETECTION_ENABLED)</label>
                                        @php $dupEnabled = old('DUPLICATE_DETECTION_ENABLED', $settings['DUPLICATE_DETECTION_ENABLED'] ?? 'true'); @endphp
                                        <select name="DUPLICATE_DETECTION_ENABLED" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="true" @selected($dupEnabled === 'true')>مفعّل</option>
                                            <option value="false" @selected($dupEnabled === 'false')>معطّل</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">يحجز للمراجعة اليدوية أي ملف بنفس محتوى ملف سبق إرساله لنفس الرقم مؤخراً، بدل إرساله تلقائياً مرة ثانية.</p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">نافذة كشف التكرار بالدقائق (DUPLICATE_DETECTION_WINDOW_MINUTES)</label>
                                        <input type="number" min="1" name="DUPLICATE_DETECTION_WINDOW_MINUTES" value="{{ old('DUPLICATE_DETECTION_WINDOW_MINUTES', $settings['DUPLICATE_DETECTION_WINDOW_MINUTES'] ?? '60') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">حد الثقة المكتسبة (LEARNED_TRUST_THRESHOLD)</label>
                                        <input type="number" min="0" name="LEARNED_TRUST_THRESHOLD" value="{{ old('LEARNED_TRUST_THRESHOLD', $settings['LEARNED_TRUST_THRESHOLD'] ?? '2') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-400 mt-1">عدد مرات الموافقة اليدوية على نفس الرقم من نفس المصدر منخفض الثقة قبل تخطي المراجعة تلقائياً في المرات القادمة. رفض واحد يُسقط الثقة فوراً. 0 يعطّل الميزة.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Conversation Distribution -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">توزيع المحادثات الجديدة (Conversation Distribution)</h3>

                                @php $distributionMode = old('CONVERSATION_DISTRIBUTION_MODE', $settings['CONVERSATION_DISTRIBUTION_MODE'] ?? 'manual'); @endphp

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">وضع التعيين (CONVERSATION_DISTRIBUTION_MODE)</label>
                                    <select name="CONVERSATION_DISTRIBUTION_MODE" id="distribution-mode" class="w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="document.getElementById('distribution-users-box').style.display = this.value === 'specific' ? 'block' : 'none';">
                                        <option value="manual" @selected($distributionMode === 'manual')>يدوي (بلا تعيين تلقائي — التعيين من صفحة المحادثات فقط)</option>
                                        <option value="specific" @selected($distributionMode === 'specific')>تلقائي لمستخدمين محددين</option>
                                        <option value="all" @selected($distributionMode === 'all')>تلقائي لكل الموظفين (role = agent) المتاحين للتعيين</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">
                                        عند التعيين التلقائي (أي وضع غير "يدوي")، تذهب كل محادثة جديدة تلقائياً لمن لديه حالياً <strong>أقل عدد محادثات مفتوحة</strong> من المجموعة المؤهّلة — توازن حمل حقيقي، وليس تناوباً دورياً أعمى لا يراعي تراكم محادثات موظف معيّن.
                                        قواعد الأتمتة (تعيين حسب رقم/كلمة مفتاحية من صفحة "الأتمتة") تبقى تعمل بمعزل عن هذا الإعداد وتُطبَّق بعده، فتستطيع أن تُلغي التعيين التلقائي لحالات محددة.
                                    </p>
                                </div>

                                <div id="distribution-users-box" class="mb-2" style="display: {{ $distributionMode === 'specific' ? 'block' : 'none' }};">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">المستخدمون المشمولون بالتوزيع (CONVERSATION_DISTRIBUTION_USER_IDS)</label>
                                    @php
                                        $selectedIds = array_filter(array_map('trim', explode(',', old('CONVERSATION_DISTRIBUTION_USER_IDS', $settings['CONVERSATION_DISTRIBUTION_USER_IDS'] ?? ''))));
                                    @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 bg-white p-3 rounded-md border border-gray-200">
                                        @forelse($assignableUsers as $user)
                                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                                <input type="checkbox" name="_distribution_user_checkbox[]" value="{{ $user->id }}" {{ in_array((string) $user->id, $selectedIds, true) ? 'checked' : '' }} onchange="document.getElementById('distribution-user-ids-hidden').value = Array.from(document.querySelectorAll('input[name=\'_distribution_user_checkbox[]\']:checked')).map(el => el.value).join(',');">
                                                {{ $user->name }}
                                                <span class="text-xs text-gray-400">({{ $user->role }}{{ !$user->is_available_for_assignment ? ' — غير متاح حالياً' : '' }})</span>
                                            </label>
                                        @empty
                                            <p class="text-sm text-gray-400">لا يوجد مستخدمون بعد.</p>
                                        @endforelse
                                    </div>
                                    <input type="hidden" name="CONVERSATION_DISTRIBUTION_USER_IDS" id="distribution-user-ids-hidden" value="{{ implode(',', $selectedIds) }}">
                                    <p class="text-xs text-gray-400 mt-1">مستخدم "غير متاح حالياً" (راجع صفحة إدارة المستخدمين) يُستبعد تلقائياً من التوزيع حتى لو بقي محدداً هنا — مفيد لاستثنائه مؤقتاً (إجازة) بلا تعديل هذه القائمة.</p>
                                </div>
                            </div>

                            <!-- System Health Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">صحة النظام (System Health)</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">حد التنبيه لتراكم المهام (HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD)</label>
                                        <input type="number" name="HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD" value="{{ old('HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD', $settings['HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD'] ?? '50') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1">
                                        <p class="text-xs text-gray-400 mt-1">فوق كم مهمة متراكمة في طابور المعالجة يُرسَل تنبيه واتساب فوري للمسؤول.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">فترة تهدئة التنبيهات (دقيقة) (HEALTH_ALERT_COOLDOWN_MINUTES)</label>
                                        <input type="number" name="HEALTH_ALERT_COOLDOWN_MINUTES" value="{{ old('HEALTH_ALERT_COOLDOWN_MINUTES', $settings['HEALTH_ALERT_COOLDOWN_MINUTES'] ?? '60') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0">
                                        <p class="text-xs text-gray-400 mt-1">بعد إرسال تنبيه صحة النظام، كم دقيقة ننتظر قبل السماح بتكراره. اضبطه على 0 لتعطيل التنبيهات كلياً.</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-8 flex justify-end">
                            <x-primary-button>
                                {{ __('حفظ الإعدادات') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
