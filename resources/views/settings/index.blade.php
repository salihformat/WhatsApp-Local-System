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
                            
                            <!-- Central API Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">الواجهة المركزية (Central API)</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">معرف الشركة (CENTRAL_API_COMPANY_ID)</label>
                                    <input type="text" name="CENTRAL_API_COMPANY_ID" value="{{ old('CENTRAL_API_COMPANY_ID', env('CENTRAL_API_COMPANY_ID')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">رمز المصادقة (CENTRAL_API_TOKEN)</label>
                                    <input type="text" name="CENTRAL_API_TOKEN" value="{{ old('CENTRAL_API_TOKEN', env('CENTRAL_API_TOKEN')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">محاولات إعادة الاتصال (CENTRAL_API_RETRY_ATTEMPTS)</label>
                                    <input type="number" name="CENTRAL_API_RETRY_ATTEMPTS" value="{{ old('CENTRAL_API_RETRY_ATTEMPTS', env('CENTRAL_API_RETRY_ATTEMPTS')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">تأخير إعادة الاتصال (CENTRAL_API_RETRY_DELAY)</label>
                                    <input type="number" name="CENTRAL_API_RETRY_DELAY" value="{{ old('CENTRAL_API_RETRY_DELAY', env('CENTRAL_API_RETRY_DELAY')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Local Retry Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">إعادة المحاولة (Local Retry)</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الحد الأقصى لمحاولات إعادة الإرسال (MAX_RETRY_ATTEMPTS)</label>
                                    <input type="number" name="MAX_RETRY_ATTEMPTS" value="{{ old('MAX_RETRY_ATTEMPTS', env('MAX_RETRY_ATTEMPTS')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">تأخير إعادة الإرسال بالدقائق (RETRY_DELAY_MINUTES)</label>
                                    <input type="number" name="RETRY_DELAY_MINUTES" value="{{ old('RETRY_DELAY_MINUTES', env('RETRY_DELAY_MINUTES')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Local Monitoring Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">المراقبة المحلية (Local Monitoring)</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">مسار المجلد (MONITORING_FOLDER_PATH)</label>
                                    <input type="text" name="MONITORING_FOLDER_PATH" value="{{ old('MONITORING_FOLDER_PATH', env('MONITORING_FOLDER_PATH', env('MONITOR_FOLDER_PATH'))) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">فترة الفحص بالثواني (MONITORING_INTERVAL_SECONDS)</label>
                                    <input type="number" name="MONITORING_INTERVAL_SECONDS" value="{{ old('MONITORING_INTERVAL_SECONDS', env('MONITORING_INTERVAL_SECONDS', env('MONITOR_INTERVAL_SECONDS'))) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">نص الرسالة (MONITORING_MESSAGE_TEXT)</label>
                                    <textarea name="MONITORING_MESSAGE_TEXT" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('MONITORING_MESSAGE_TEXT', env('MONITORING_MESSAGE_TEXT', env('MONITOR_MESSAGE_TEXT'))) }}</textarea>
                                </div>
                            </div>

                            <!-- Device Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">بيانات الجهاز (Device Info)</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم الجهاز (DEVICE_NAME)</label>
                                    <input type="text" name="DEVICE_NAME" value="{{ old('DEVICE_NAME', env('DEVICE_NAME')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الموقع (LOCATION)</label>
                                    <input type="text" name="LOCATION" value="{{ old('LOCATION', env('LOCATION')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع الباقة (PLAN_TYPE)</label>
                                    <input type="text" name="PLAN_TYPE" value="{{ old('PLAN_TYPE', env('PLAN_TYPE')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- File Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">إعدادات الملفات (File Settings)</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">مسار التخزين (FILE_STORAGE_PATH)</label>
                                        <input type="text" name="FILE_STORAGE_PATH" value="{{ old('FILE_STORAGE_PATH', env('FILE_STORAGE_PATH')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">أقصى حجم بالميغابايت (FILE_MAX_SIZE_MB)</label>
                                        <input type="number" name="FILE_MAX_SIZE_MB" value="{{ old('FILE_MAX_SIZE_MB', env('FILE_MAX_SIZE_MB')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الحذف التلقائي بعد (أيام) (FILE_AUTO_DELETE_DAYS)</label>
                                        <input type="number" name="FILE_AUTO_DELETE_DAYS" value="{{ old('FILE_AUTO_DELETE_DAYS', env('FILE_AUTO_DELETE_DAYS')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    
                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الأنواع المسموحة (FILE_ALLOWED_TYPES) <span class="text-xs text-gray-500">مفصول بفاصلة</span></label>
                                        <input type="text" name="FILE_ALLOWED_TYPES" value="{{ old('FILE_ALLOWED_TYPES', env('FILE_ALLOWED_TYPES')) }}" placeholder="pdf,jpg,png" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>

                            <!-- PDF/DOCX Phone Extraction Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">استخراج رقم الجوال من محتوى الملفات (PDF/DOCX)</h3>
                                <p class="text-xs text-gray-500 mb-4">تُستخدم هذه الإعدادات عندما لا يحتوي اسم الملف على رقم جوال، فيبحث النظام داخل محتوى الملف نفسه.</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمات البحث عن رقم الجوال (PHONE_EXTRACTION_LABELS) <span class="text-xs text-gray-500">مفصولة بفاصلة</span></label>
                                        <textarea name="PHONE_EXTRACTION_LABELS" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="رقم الجوال,الجوال,جوال,phone,mobile">{{ old('PHONE_EXTRACTION_LABELS', env('PHONE_EXTRACTION_LABELS')) }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">إذا كانت المطابقة قريبة من إحدى هذه الكلمات يُعتبر الرقم بعدها رقم جوال العميل.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمات الاستبعاد (PHONE_EXTRACTION_EXCLUDE_CONTEXT) <span class="text-xs text-gray-500">مفصولة بفاصلة</span></label>
                                        <textarea name="PHONE_EXTRACTION_EXCLUDE_CONTEXT" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="المحل,الشركة,مكتبنا,store,shop,company">{{ old('PHONE_EXTRACTION_EXCLUDE_CONTEXT', env('PHONE_EXTRACTION_EXCLUDE_CONTEXT')) }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">إذا ظهرت إحدى هذه الكلمات قبل رقم الجوال مباشرة (مثل "هاتف المحل") يتم تجاهل هذا الرقم لأنه رقم الجهة المُصدرة وليس العميل.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمات البحث عن رقم الملف (FILE_NUMBER_LABELS) <span class="text-xs text-gray-500">مفصولة بفاصلة</span></label>
                                        <textarea name="FILE_NUMBER_LABELS" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="رقم الملف,الملف رقم,ملف رقم,file no">{{ old('FILE_NUMBER_LABELS', env('FILE_NUMBER_LABELS')) }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">عند العثور على رقم بجانب إحدى هذه الكلمات يبحث النظام عن جهة اتصال بنفس رقم الملف ويرسل لرقم جوالها.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">نمط المطابقة (PHONE_MATCH_MODE)</label>
                                        <select name="PHONE_MATCH_MODE" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @php $matchMode = old('PHONE_MATCH_MODE', env('PHONE_MATCH_MODE', 'partial')); @endphp
                                            <option value="partial" @selected($matchMode === 'partial')>جزئي (الكلمة كجزء من نص أطول)</option>
                                            <option value="exact" @selected($matchMode === 'exact')>كامل (الكلمة ككلمة مستقلة فقط)</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">"جزئي" يطابق "جوال" حتى داخل "جوالكم"، أما "كامل" فيتطلب الكلمة منفصلة تماماً.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">السماح بالاستخراج بلا تسمية (ENABLE_UNLABELED_PHONE_FALLBACK)</label>
                                        <select name="ENABLE_UNLABELED_PHONE_FALLBACK" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @php $unlabeledEnabled = old('ENABLE_UNLABELED_PHONE_FALLBACK', env('ENABLE_UNLABELED_PHONE_FALLBACK', 'true')); @endphp
                                            <option value="true" @selected($unlabeledEnabled === 'true')>مفعّل (يبحث عن أي رقم يشبه جوال سعودي بلا تسمية كحل أخير)</option>
                                            <option value="false" @selected($unlabeledEnabled === 'false')>معطّل (إن لم توجد تسمية صريحة يُنقل الملف لمجلد "فشلت")</option>
                                        </select>
                                    </div>

                                    <div class="mb-4 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">مصادر تتطلب مراجعة يدوية قبل الإرسال (PHONE_REVIEW_REQUIRED_SOURCES) <span class="text-xs text-gray-500">مفصولة بفاصلة</span></label>
                                        <input type="text" name="PHONE_REVIEW_REQUIRED_SOURCES" value="{{ old('PHONE_REVIEW_REQUIRED_SOURCES', env('PHONE_REVIEW_REQUIRED_SOURCES')) }}" placeholder="unlabeled_fallback,corrupted_fallback,env_fallback" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-xs text-gray-400 mt-1">
                                            بدلاً من الإرسال التلقائي مباشرة، أي ملف يُستخرج رقمه من أحد هذه المصادر (منخفضة الثقة) يُحجز في تبويب "بانتظار المراجعة" بصفحة متابعة الإرسال حتى تُوافق عليه يدوياً.
                                            القيم الممكنة: <code dir="ltr">filename, label, file_number, unlabeled_fallback, corrupted_fallback, env_fallback</code>.
                                            اتركه فارغاً للإرسال التلقائي دائماً (بدون مراجعة).
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Conversation Distribution -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">توزيع المحادثات الجديدة (Conversation Distribution)</h3>

                                @php $distributionMode = old('CONVERSATION_DISTRIBUTION_MODE', env('CONVERSATION_DISTRIBUTION_MODE', 'manual')); @endphp

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
                                        $selectedIds = array_filter(array_map('trim', explode(',', old('CONVERSATION_DISTRIBUTION_USER_IDS', env('CONVERSATION_DISTRIBUTION_USER_IDS', '')))));
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

                            <!-- Smart Printing Status Replies -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
                                <h3 class="text-lg font-bold mb-4 text-indigo-700 border-b pb-2">إشعارات حالة الطباعة الذكية</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">رد فوري عند استلام الطلب (PRINTING_REPLY_ACK_ON_RECEIPT)</label>
                                        <select name="PRINTING_REPLY_ACK_ON_RECEIPT" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @php $replyAck = old('PRINTING_REPLY_ACK_ON_RECEIPT', env('PRINTING_REPLY_ACK_ON_RECEIPT', 'true')); @endphp
                                            <option value="true" @selected($replyAck === 'true')>مفعّل ("📥 تم استلام طلبك وجاري تنفيذه" فور تسجيل الطلب)</option>
                                            <option value="false" @selected($replyAck === 'false')>معطّل</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">رسالة منفصلة عن رد النتيجة النهائية أدناه — لطمأنة العميل أن ملفه وصل بشكل صحيح.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الرد على من طلب الطباعة (PRINTING_REPLY_STATUS_TO_SENDER)</label>
                                        <select name="PRINTING_REPLY_STATUS_TO_SENDER" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @php $replySender = old('PRINTING_REPLY_STATUS_TO_SENDER', env('PRINTING_REPLY_STATUS_TO_SENDER', 'true')); @endphp
                                            <option value="true" @selected($replySender === 'true')>مفعّل (يصل العميل رسالة تلقائية بنجاح/فشل طباعة ملفه)</option>
                                            <option value="false" @selected($replySender === 'false')>معطّل</option>
                                        </select>
                                        <p class="text-xs text-gray-400 mt-1">لا يُرسَل عند كل محاولة فاشلة، فقط عند النجاح أو الفشل النهائي بعد استنفاد كل المحاولات.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">تنبيه فني لصاحب المنشأة عند الفشل (PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE)</label>
                                        <select name="PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @php $notifyOwner = old('PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE', env('PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE', 'true')); @endphp
                                            <option value="true" @selected($notifyOwner === 'true')>مفعّل (يصل الخطأ التقني الكامل لرقم PRINTER_ALERT_PHONE)</option>
                                            <option value="false" @selected($notifyOwner === 'false')>معطّل</option>
                                        </select>
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
