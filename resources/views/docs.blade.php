<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="prose prose-lg dark:prose-invert max-w-none" dir="rtl">
                        <h1 class="text-3xl font-bold mb-6 text-[#f53003]">دليل استخدام نظام الواتساب المحلي</h1>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">1. كيفية التشغيل في الجهاز المحلي</h2>
                            <p class="mb-4">لتشغيل النظام على جهازك الخاص، يرجى اتباع الخطوات التالية:</p>
                            <ul class="list-disc list-inside space-y-2 mr-4">
                                <li>تأكد من تثبيت بيئة العمل (XAMPP أو Laragon).</li>
                                <li>قم بإنشاء قاعدة بيانات جديدة باسم <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">whatsapp_local</code>.</li>
                                <li>قم بنسخ ملف <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">.env.example</code> إلى <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">.env</code> وقم بتحديث بيانات الاتصال بقاعدة البيانات.</li>
                                <li>افتح نافذة الأوامر (Terminal) في مجلد المشروع وقم بتشغيل الأوامر التالية:
                                    <pre class="bg-gray-900 text-gray-100 p-4 rounded mt-2 dir-ltr">composer install
php artisan migrate --seed
php artisan serve --port=8001</pre>
                                </li>
                                <li>يمكنك الآن الدخول للموقع عبر الرابط: <a href="http://127.0.0.1:8001" class="text-blue-500 underline">http://127.0.0.1:8001</a></li>
                            </ul>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">2. الربط مع النظام المركزي (Central System)</h2>
                            <p class="mb-4">ليتمكن النظام المحلي من إرسال الرسائل عبر السيرفر الرئيسي، يجب ضبط إعدادات الربط في ملف <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">.env</code>:</p>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg space-y-4">
                                <div>
                                    <h3 class="font-bold text-[#f53003]">CENTRAL_API_URL</h3>
                                    <p class="text-sm">عنوان الرابط الخاص بواجهة برمجة التطبيقات (API) للنظام المركزي. مثال: <code class="bg-white dark:bg-gray-800 px-1">http://your-central-domain.com/api</code></p>
                                </div>
                                
                                <div>
                                    <h3 class="font-bold text-[#f53003]">CENTRAL_API_COMPANY_ID (رقم الشركة)</h3>
                                    <p class="text-sm">هذا الرقم يتم الحصول عليه من لوحة تحكم النظام المركزي (قسم الشركات). يمثل الهوية الفريدة لفرعك أو شركتك.</p>
                                </div>

                                <div>
                                    <h3 class="font-bold text-[#f53003]">CENTRAL_API_TOKEN (كود الحماية/التوكن)</h3>
                                    <p class="text-sm">هو مفتاح الأمان (Security Token) الذي يتم إنشاؤه في النظام المركزي لكل شركة. يوضع هنا لضمان أن الاتصال مشفر ومصرح به.</p>
                                </div>

                                <div>
                                    <h3 class="font-bold text-[#f53003]">API_ENCRYPTION_KEY</h3>
                                    <p class="text-sm">مفتاح تشفير إضافي للبيانات الحساسة. يمكنك توليده باستخدام الأمر التالي في الـ Terminal:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-2 rounded mt-1 text-xs dir-ltr">php -r "echo bin2hex(random_bytes(32));"</pre>
                                </div>

                                <div>
                                    <h3 class="font-bold text-[#f53003]">MONITOR_FOLDER_PATH (مجلد المراقبة)</h3>
                                    <p class="text-sm">المسار الكامل للمجلد الذي سيقوم النظام بمراقبته لسحب الملفات وإرسالها تلقائياً. مثال: <code class="bg-white dark:bg-gray-800 px-1">C:/PrintMonitor</code></p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">3. شرح صفحات النظام</h2>
                            
                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">لوحة التحكم (Dashboard)</h3>
                                    <p>تعتبر المركز الرئيسي للنظام، حيث تظهر لك إحصائيات سريعة عن عدد الرسائل المرسلة، الفاشلة، والمنتظرة. كما تحتوي على أزرار للتحكم اليدوي مثل "فحص المجلد" و "إعادة محاولة الرسائل الفاشلة".</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">إدارة الرسائل (Messages)</h3>
                                    <p>هنا يمكنك عرض جميع الرسائل التي تم معالجتها من قبل النظام. يمكنك رؤية حالة كل رسالة (تم الإرسال، فشل، تم التسليم، تمت القراءة). كما يمكنك تحديد رسائل معينة للقيام بعمليات جماعية أو إعادة إرسال رسالة محددة.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">إدارة المستخدمين (Users)</h3>
                                    <p>تسمح للمسؤولين بإضافة مستخدمين جدد للنظام أو تعديل بيانات المستخدمين الحاليين وصلاحياتهم.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">الملف الشخصي (Profile)</h3>
                                    <p>صفحة خاصة بكل مستخدم لتغيير بياناته الشخصية مثل الاسم، البريد الإلكتروني، وكلمة المرور.</p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">4. تشغيل المعالجة الخلفية والجدولة</h2>
                            <p class="mb-4">لضمان إرسال الرسائل في الخلفية ومعالجة الملفات تلقائياً، يجب تشغيل الأوامر التالية:</p>
                            
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2">أولاً: معالج الطوابير (Queue Worker)</h3>
                                    <p class="text-sm mb-2">هذا الأمر مسؤول عن إرسال الرسائل، ومزامنة جهات الاتصال، ومعالجة أحداث الـ Webhooks:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan queue:work --queue=contacts-sync,webhooks,default</pre>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2">ثانياً: المجدول (Scheduler)</h3>
                                    <p class="text-sm mb-2">هذا الأمر مسؤول عن فحص المجلدات وتنفيذ المهام الدورية:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan schedule:run</pre>
                                </div>
                            </div>

                            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <h3 class="font-bold mb-2 text-blue-700 dark:text-blue-400">نظام ويندوز (Windows)</h3>
                                <p class="text-sm mb-4">في بيئة ويندوز، يمكنك تشغيل المجدول تلقائياً باستخدام إحدى الطريقتين:</p>
                                
                                <h4 class="font-bold text-sm">الطريقة الأولى: جدولة المهام (Task Scheduler)</h4>
                                <ol class="list-decimal list-inside text-sm space-y-1 mr-4 mb-4">
                                    <li>افتح "Task Scheduler" في ويندوز.</li>
                                    <li>أنشئ مهمة جديدة (Create Basic Task).</li>
                                    <li>اختر التشغيل "Daily" ثم كررها كل "1 minute" من الإعدادات المتقدمة.</li>
                                    <li>في "Action" اختر "Start a Program" وحدد ملف <code class="bg-white px-1">run-scheduler.bat</code> الموجود في مجلد المشروع.</li>
                                </ol>

                                <h4 class="font-bold text-sm">الطريقة الثانية: ملف Batch يعمل بشكل مستمر (Loop)</h4>
                                <p class="text-sm">يمكنك تعديل ملف <code class="bg-white px-1">run-scheduler.bat</code> ليقوم بالتشغيل كل دقيقة تلقائياً بإضافة سطر التكرار:</p>
                                <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr text-xs mt-2">
:loop
php artisan schedule:run
timeout /t 60 /nobreak
goto loop</pre>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">5. التشغيل على خوادم الاستضافة (Hosting)</h2>
                            <p class="mb-4">عند رفع المشروع على استضافة (مثل VPS أو Shared Hosting)، يجب إعداد التالي:</p>
                            
                            <h3 class="text-lg font-bold mb-2">1. إعداد الـ Cron Job</h3>
                            <p class="mb-2">قم بإضافة السطر التالي في إعدادات Cron Job في لوحة تحكم الاستضافة (تنفذ كل دقيقة):</p>
                            <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1</pre>

                            <h3 class="text-lg font-bold mb-2 mt-4">2. تشغيل الـ Worker بشكل مستمر</h3>
                            <p class="mb-2">يفضل استخدام **Supervisor** على خوادم Linux لضمان بقاء الـ Worker يعمل دائماً. مثال لإعداد بسيط:</p>
                            <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr text-xs">
[program:whatsapp-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --queue=contacts-sync,webhooks,default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log</pre>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">6. مزامنة جهات الاتصال يدوياً (Contacts Sync)</h2>
                            <p class="mb-4">تتم مزامنة جهات الاتصال تلقائياً عبر المهام المجدولة، ولكن يمكنك إجراؤها يدوياً في أي وقت:</p>
                            
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-purple-500">
                                    <h3 class="text-lg font-bold mb-2">1. المزامنة عبر الطابور (ينصح به):</h3>
                                    <p class="text-sm mb-2">هذا الأمر يضع مهمة المزامنة في الطابور لمعالجتها في الخلفية (يتطلب تشغيل Queue Worker):</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan contacts:sync</pre>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2">2. المزامنة الفورية (بدون طابور):</h3>
                                    <p class="text-sm mb-2">لتنفيذ المزامنة فوراً ورؤية النتائج مباشرة في الشاشة (مفيد لمعرفة سبب المشكلة أو الخطأ إن وجد):</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan contacts:sync --now</pre>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">7. ملاحظات هامة</h2>
                            <ul class="list-disc list-inside space-y-2 mr-4 text-gray-700 dark:text-gray-300">
                                <li>النظام يعتمد على ملفات يتم وضعها في مجلد محدد ليقوم بمعالجتها وإرسالها عبر الواتساب.</li>
                                <li>تأكد من أن خدمة "الجدولة" (Scheduler) تعمل في الخلفية لضمان استمرارية الإرسال.</li>
                                <li>يمكنك متابعة حالة الربط مع الخدمة المركزية من خلال لوحة التحكم.</li>
                            </ul>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
