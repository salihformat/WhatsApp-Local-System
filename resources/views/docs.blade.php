<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="prose prose-lg dark:prose-invert max-w-none" dir="rtl">
                        <h1 class="text-3xl font-bold mb-6 text-[#f53003]">دليل استخدام نظام الواتساب المحلي</h1>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">1. كيفية التشغيل في الجهاز المحلي</h2>
                            <p class="mb-4">لتشغيل النظام على جهازك الخاص لأول مرة، يرجى اتباع الخطوات التالية:</p>
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
                            <div class="mt-4 bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border-r-4 border-indigo-400">
                                <p class="text-sm">هذه الخطوات كافية للتجربة والتطوير فقط. للاستخدام اليومي الفعلي على جهاز المنشأة (يعمل تلقائياً بعد كل إقلاع بلا تدخل يدوي، على منفذ ثابت، مع الطوابير والمجدول)، راجع القسم <strong>8. التشغيل التلقائي الكامل</strong> أدناه — هذه هي الطريقة الموصى بها فعلياً وليست الأوامر اليدوية أعلاه.</p>
                            </div>
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
                                    <p class="text-sm">المسار الكامل للمجلد الذي سيقوم النظام بمراقبته لسحب الملفات وإرسالها تلقائياً. مثال: <code class="bg-white dark:bg-gray-800 px-1">C:/PrintMonitor</code> — راجع القسم 6 أدناه لشرح كامل لطريقة استخدام هذا المجلد.</p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">3. شرح صفحات النظام</h2>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">لوحة التحكم (Dashboard)</h3>
                                    <p>تعتبر المركز الرئيسي للنظام، حيث تظهر لك إحصائيات سريعة عن عدد الرسائل المرسلة، الفاشلة، والمنتظرة. كما تحتوي على أزرار للتحكم اليدوي مثل "فحص المجلد" و "إعادة محاولة الرسائل الفاشلة" (للمسؤولين فقط).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">المحادثات والرسائل (Conversations / Messages)</h3>
                                    <p>عرض كل المحادثات والرسائل الواردة والصادرة مع حالة كل رسالة (تم الإرسال، فشل، تم التسليم، تمت القراءة)، مع إمكانية إعادة إرسال رسالة فاشلة أو تنفيذ عمليات جماعية.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">أدوات PDF (PDF Tools)</h3>
                                    <p>دمج عدة ملفات PDF في ملف واحد، تقسيم ملف PDF، وضغط الصور — متاحة لكل المستخدمين المسجّلين وليست إدارية.</p>
                                </div>

                                <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border-r-4 border-indigo-400">
                                    <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 mb-1">الصفحات التالية مخصصة للمسؤولين (Admin) فقط، وتظهر ضمن قائمة "الإدارة" في الشريط العلوي:</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">إدارة المستخدمين (Users)</h3>
                                    <p>إضافة مستخدمين جدد أو تعديل بيانات المستخدمين الحاليين وصلاحياتهم (مسؤول/مشرف/موظف).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">الإعدادات (Settings)</h3>
                                    <p>ضبط إعدادات الربط مع المركزي، إعدادات الملفات وإعادة المحاولة، بالإضافة إلى <strong>إعدادات استخراج رقم الجوال من محتوى الملفات</strong> (كلمات البحث عن رقم الجوال، كلمات الاستبعاد، نمط المطابقة، مصادر تتطلب مراجعة يدوية) و<strong>إعدادات إشعارات حالة الطباعة الذكية</strong> — بلا الحاجة لتعديل ملف <code>.env</code> يدوياً.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">تقارير الأداء (Reports)</h3>
                                    <p>تقارير إحصائية عن أداء الإرسال ونشاط المحادثات.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">الطابعات وقواعد التوجيه (Printers)</h3>
                                    <p>إضافة/تعديل الطابعات المتصلة بالجهاز، تحديد طابعة افتراضية، فحص حالة كل طابعة فوراً (ورق/حبر/اتصال)، وتحديد أي طابعة "موثوقة" لإرسال تأكيد طباعة حقيقي للعميل (راجع القسم 4 أدناه للتفصيل).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">متابعة الإرسال (Print Monitor)</h3>
                                    <p>عرض حي لمجلد المراقبة (C:\PrintMonitor): الملفات قيد الانتظار، بانتظار المراجعة اليدوية، قيد المعالجة، أُرسلت بنجاح، أو فشلت — مع رقم الجوال المُستخرج، سبب الفشل، وتفاصيل كاملة عن آلية استخراج الرقم لكل ملف (أي كلمة طابقت، وأي أرقام استُبعدت ولماذا).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">سجل عمليات الطباعة (Print Jobs)</h3>
                                    <p>سجل كامل لكل مهمة طباعة: وقت وصول الطلب، وقت اكتمال الطباعة، المدة المستغرقة، عدد المحاولات، وسبب الفشل إن وُجد.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">الأتمتة (Automation Rules)</h3>
                                    <p>قواعد تلقائية لتعيين موظف، إضافة ملاحظة داخلية، أو رد تلقائي فوري عند مطابقة رسالة واردة لشروط معينة — مع حماية مدمجة ضد حلقات الرد التلقائي.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">صحة النظام (System Health)</h3>
                                    <p>رسم بياني لتتبع الرسائل المعلّقة/الفاشلة وتراكم قائمة الانتظار بمرور الوقت، وحالة الاتصال بالنظام المركزي.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">سجل التدقيق (Audit Log)</h3>
                                    <p>سجل كامل لكل تغيير إداري (من غيّر ماذا ومتى) — تسجيل الدخول، تغييرات الإعدادات، إدارة المستخدمين، الطابعات، وغيرها.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h3 class="text-xl font-bold mb-2 text-[#f53003]">الملف الشخصي (Profile)</h3>
                                    <p>صفحة خاصة بكل مستخدم لتغيير بياناته الشخصية مثل الاسم، البريد الإلكتروني، وكلمة المرور.</p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">4. الطباعة الذكية بالتفصيل (Smart Printing)</h2>
                            <p class="mb-4">عند وصول ملف PDF عبر واتساب (أو وضعه في مجلد المراقبة) يحتوي على كلمة طباعة (مثل "اطبع"، "print")، يعمل النظام تلقائياً وفق الخطوات التالية:</p>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2">1. استخراج رقم الجوال / رقم الملف</h3>
                                    <p class="text-sm">من اسم الملف أولاً، ثم من محتوى الملف (PDF/DOCX) عبر كلمات دلالة قابلة للتخصيص من صفحة الإعدادات. إذا فشلت كل الطرق العادية بسبب تلف طبقة النص (مشكلة ترميز شائعة في بعض ملفات PDF الممسوحة ضوئياً)، يحاول النظام أخيراً قراءة الملف كصورة عبر Tesseract OCR (راجع القسم 5). إذا كان الاستخراج من مصدر منخفض الثقة (بلا تسمية صريحة)، يُحجز الملف في تبويب "بانتظار المراجعة" بصفحة متابعة الإرسال بدل الإرسال التلقائي المباشر.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2">2. رد فوري بالاستلام</h3>
                                    <p class="text-sm">يصل للعميل فوراً: "📥 تم استلام طلب طباعة ملفك وجاري تنفيذه الآن..." — قبل تنفيذ الطباعة فعلياً، ليطمئن أن ملفه وصل بشكل صحيح.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-purple-500">
                                    <h3 class="text-lg font-bold mb-2">3. الطباعة الفعلية + التحقق</h3>
                                    <p class="text-sm">يُرسل الملف للطابعة المطابقة عبر SumatraPDF (راجع القسم 5). إذا كانت الطابعة معلَّمة كـ"موثوقة" في صفحة الطابعات (تدعم إبلاغاً حقيقياً عن أعطالها)، يتحقق النظام من صحتها قبل وبعد الطباعة (نفاد ورق/حبر/اتصال). ليس كل طراز طابعة يدعم هذا — بعض التعريفات (كطابعات USB القديمة) لا تُبلّغ عن هذه الحالات إطلاقاً عبر Windows، لذا الوضع الافتراضي لكل طابعة جديدة هو "غير مؤكَّدة" حتى تتحقق يدوياً وتُفعّلها.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-orange-500">
                                    <h3 class="text-lg font-bold mb-2">4. رد النتيجة النهائية</h3>
                                    <p class="text-sm">
                                        <strong>طابعة موثوقة:</strong> يصل العميل تأكيد دقيق — "✅ تم الطباعة بنجاح" أو "❌ فشلت الطباعة، السبب: ..." (سبب مبسّط، بدون تفاصيل تقنية).<br>
                                        <strong>طابعة غير مؤكَّدة:</strong> لا يصل العميل أي تأكيد نجاح/فشل إضافي (تفادياً لتأكيد كاذب) — يكتفى برسالة الاستلام في الخطوة 2. الفشل الناتج عن خطأ برمجي حقيقي (تنزيل الملف، تعطّل أداة الطباعة، انتهاء المهلة) يُبلَّغ دائماً بصرف النظر عن هذا الإعداد.
                                    </p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2">5. تنبيه صاحب المنشأة</h3>
                                    <p class="text-sm">عند فشل طباعة نهائياً (بعد استنفاد كل المحاولات)، يصل تنبيه فني منفصل لرقم <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> بالخطأ الحقيقي الكامل — بخلاف الرسالة المبسَّطة التي يستلمها العميل.</p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                                ⚠️ ملاحظة تشغيلية هامة: أي تعديل على الكود (مثل صياغة الرسائل أو منطق الفحص) يتطلب <strong>إعادة تشغيل قائمة الانتظار (Queue Worker)</strong> من لوحة التحكم حتى يعمل — العامل يحتفظ بنسخة الكود في الذاكرة منذ آخر تشغيل ولا يقرأ الملفات المُعدَّلة تلقائياً.
                            </p>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">5. البرامج الخارجية المطلوبة</h2>
                            <p class="mb-4">النظام لا يقوم بالطباعة أو قراءة الصور بنفسه — يعتمد على برنامجين خارجيين مجانيين يجب تثبيتهما على نفس الجهاز:</p>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">SumatraPDF — لطباعة ملفات PDF تلقائياً وبصمت</h3>
                                    <p class="text-sm mb-2">برنامج مجاني وخفيف يُستخدم لإرسال ملفات PDF مباشرة للطابعة بدون فتح أي نافذة (طباعة صامتة). بدونه، ميزة الطباعة الذكية بالكامل لن تعمل.</p>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>نزّل النسخة المحمولة (Portable) من الموقع الرسمي: <a href="https://www.sumatrapdfreader.org" class="text-blue-500 underline" target="_blank">sumatrapdfreader.org</a></li>
                                        <li>ضع ملف <code class="bg-white dark:bg-gray-800 px-1">SumatraPDF.exe</code> في المسار: <code class="bg-white dark:bg-gray-800 px-1">C:/SumatraPDF/SumatraPDF.exe</code> (أو أي مسار آخر تختاره).</li>
                                        <li>تأكد أن مسار الملف مطابق تماماً لقيمة <code class="bg-white dark:bg-gray-800 px-1">SUMATRA_PDF_PATH</code> في ملف <code class="bg-white dark:bg-gray-800 px-1">.env</code> (يمكن تعديله أيضاً من صفحة الإعدادات لاحقاً إن أُضيف هناك).</li>
                                        <li>تأكد أن <code class="bg-white dark:bg-gray-800 px-1">PRINTING_ENABLED=true</code> في <code class="bg-white dark:bg-gray-800 px-1">.env</code> لتفعيل الميزة بالكامل.</li>
                                    </ol>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-teal-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">Tesseract OCR — لقراءة النصوص من الصور والملفات الممسوحة ضوئياً</h3>
                                    <p class="text-sm mb-2">يُستخدم كحل أخير لاستخراج رقم الجوال عندما تفشل الطرق العادية (مثلاً: ملف PDF عبارة عن صورة ممسوحة ضوئياً بلا طبقة نص حقيقية، أو صورة jpg/png مرسلة مباشرة). اختياري — النظام يعمل بدونه، لكن هذه الحالات تحديداً ستفشل في استخراج الرقم دون تثبيته.</p>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>نزّل المثبِّت لويندوز من: <a href="https://github.com/UB-Mannheim/tesseract/wiki" class="text-blue-500 underline" target="_blank">UB-Mannheim/tesseract (Windows builds)</a></li>
                                        <li>ثبّته بالمسار الافتراضي: <code class="bg-white dark:bg-gray-800 px-1">C:/Program Files/Tesseract-OCR</code></li>
                                        <li>أضف السطر التالي في ملف <code class="bg-white dark:bg-gray-800 px-1">.env</code>:
                                            <pre class="bg-gray-900 text-gray-100 p-2 rounded mt-1 text-xs dir-ltr">TESSERACT_BIN_PATH="C:/Program Files/Tesseract-OCR/tesseract.exe"</pre>
                                        </li>
                                        <li>تأكد عند التثبيت من اختيار حزمة اللغة العربية (Arabic) بالإضافة للإنجليزية، لدعم قراءة المستندات العربية.</li>
                                    </ol>
                                    <p class="text-xs text-gray-500 mt-2">للتحقق من التثبيت الصحيح: افتح Command Prompt ونفّذ <code class="bg-white dark:bg-gray-800 px-1">"C:\Program Files\Tesseract-OCR\tesseract.exe" --version</code> — يجب أن يظهر رقم الإصدار بلا أخطاء.</p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                                ⚠️ بعد تثبيت أو تعديل مسار أي من البرنامجين في <code>.env</code>، يجب <strong>إعادة تشغيل قائمة الانتظار (Queue Worker)</strong> من لوحة التحكم حتى يقرأ الإعداد الجديد.
                            </p>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">6. كيفية استخدام مجلد المراقبة (PrintMonitor)</h2>
                            <p class="mb-4">هذا المجلد (افتراضياً <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">C:\PrintMonitor</code>، يُضبط عبر <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">MONITOR_FOLDER_PATH</code>) هو الطريقة الثانية لإرسال ملف عبر واتساب — بدلاً من إرساله عبر محادثة واتساب فعلية، تضع الملف مباشرة في هذا المجلد على جهاز الكمبيوتر ويقوم النظام بإرساله تلقائياً.</p>

                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg mb-4">
                                <h3 class="font-bold mb-2 text-[#f53003]">خطوات الاستخدام اليومي</h3>
                                <ol class="list-decimal list-inside text-sm space-y-2 mr-4">
                                    <li>افتح المجلد <code class="bg-white dark:bg-gray-800 px-1">C:\PrintMonitor</code> في جهاز الكمبيوتر (وليس داخل الموقع).</li>
                                    <li>ضع أو انسخ ملف الفاتورة/المستند (PDF, DOCX, JPG, PNG...) مباشرة في **جذر** هذا المجلد (وليس داخل أي من المجلدات الفرعية).</li>
                                    <li>
                                        لتضمين رقم الجوال مباشرة، سمِّ الملف بحيث يحتوي رقم الجوال (9 إلى 15 رقماً) في اسمه، مثال:
                                        <code class="bg-white dark:bg-gray-800 px-1">0512345678_فاتورة.pdf</code> أو <code class="bg-white dark:bg-gray-800 px-1">966512345678.pdf</code>.
                                    </li>
                                    <li>
                                        إذا لم يحتوِ اسم الملف على رقم، سيحاول النظام قراءة الرقم (أو رقم الملف المرتبط بجهة اتصال) من **محتوى** الملف نفسه — راجع القسم 4 أعلاه لتفاصيل هذه الآلية، وصفحة الإعدادات لتخصيص الكلمات الدلالية المستخدمة في البحث.
                                    </li>
                                    <li>خلال ثوانٍ (حسب <code class="bg-white dark:bg-gray-800 px-1">MONITORING_INTERVAL_SECONDS</code>، افتراضياً كل دقيقة عبر الجدولة)، سيلتقط النظام الملف تلقائياً ويرسله.</li>
                                </ol>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <h3 class="font-bold mb-2 text-[#f53003]">المجلدات الفرعية (تُنشأ تلقائياً — لا تضع ملفات فيها يدوياً)</h3>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><code class="bg-white dark:bg-gray-800 px-1">processing/</code> — الملفات قيد المعالجة والإرسال حالياً.</li>
                                    <li><code class="bg-white dark:bg-gray-800 px-1">review/</code> — ملفات استُخرج رقمها من مصدر منخفض الثقة وتحتاج موافقتك اليدوية من صفحة "متابعة الإرسال" قبل إرسالها فعلياً.</li>
                                    <li><code class="bg-white dark:bg-gray-800 px-1">archive/</code> — الملفات التي أُرسلت بنجاح.</li>
                                    <li><code class="bg-white dark:bg-gray-800 px-1">failed/</code> — الملفات التي فشل إرسالها (غالباً بسبب تعذّر العثور على رقم جوال صالح) — يمكنك مراجعة السبب من صفحة "متابعة الإرسال".</li>
                                </ul>
                                <p class="text-sm mt-3">لمتابعة حالة كل ملف لحظياً (وصل، بانتظار مراجعة، نجح، فشل ولماذا)، استخدم صفحة <strong>متابعة الإرسال</strong> بدل فتح هذه المجلدات يدوياً.</p>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">7. تشغيل المعالجة الخلفية والجدولة يدوياً (للتجربة فقط)</h2>
                            <p class="mb-4">الطريقة التالية مفيدة للتجربة أو تشخيص مشكلة مباشرة على الشاشة. للاستخدام اليومي الفعلي استخدم التشغيل التلقائي (القسم 8) بدلاً من هذه الأوامر اليدوية.</p>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2">أولاً: معالج الطوابير (Queue Worker)</h3>
                                    <p class="text-sm mb-2">هذا الأمر مسؤول عن إرسال الرسائل، معالجة الطباعة، ومزامنة جهات الاتصال:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan queue:work --queue=contacts-sync,webhooks,default</pre>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2">ثانياً: المجدول (Scheduler)</h3>
                                    <p class="text-sm mb-2">هذا الأمر مسؤول عن فحص مجلد المراقبة وتنفيذ المهام الدورية (مزامنة، فحص الطابعات، فحص انتهاء صلاحية الملفات، إلخ):</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr">php artisan schedule:run</pre>
                                    <p class="text-xs text-gray-500 mt-2">هذا الأمر ينفّذ فحصاً واحداً فقط ثم يتوقف — يحتاج تكراراً كل دقيقة. البديل: <code class="bg-white dark:bg-gray-800 px-1">php artisan schedule:work</code> يبقى يعمل باستمرار وينفّذ كل مهمة في وقتها تلقائياً (هذا هو الأمر المستخدم فعلياً في التشغيل التلقائي بالقسم 8).</p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">8. التشغيل التلقائي الكامل (سكربتات الإعداد الجاهزة)</h2>
                            <p class="mb-4">بدل تشغيل الأوامر السابقة يدوياً في نوافذ Terminal تبقى مفتوحة (وتتوقف عند إغلاقها أو إعادة تشغيل الجهاز)، يوفّر النظام سكربتات جاهزة في مجلد <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">scripts/</code> تُعِدّ تشغيلاً تلقائياً كاملاً يعمل من نفسه بعد كل إقلاع للجهاز. هذه هي الطريقة الموصى بها لجهاز العمل الفعلي.</p>

                            <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border-r-4 border-indigo-400 mb-4">
                                <p class="text-sm">ماذا يفعل هذا الإعداد فعلياً؟</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4 mt-2">
                                    <li>ينسخ نسخة معزولة خاصة من Apache (لا يلمس تثبيت XAMPP الأصلي أو أي مشروع آخر على الجهاز) ويسجّلها كخدمة Windows تعمل تلقائياً على المنفذ المحدد في <code class="bg-white dark:bg-gray-800 px-1">APP_URL</code>.</li>
                                    <li>يسجّل عامل الطابور (<code class="bg-white dark:bg-gray-800 px-1">queue:work</code>) والمجدول (<code class="bg-white dark:bg-gray-800 px-1">schedule:work</code>) كمهمّتين في "جدولة المهام" (Task Scheduler) تعملان تلقائياً عند الإقلاع، وتُعيدان تشغيل نفسيهما تلقائياً في حال توقفتا لأي سبب.</li>
                                </ul>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2">للتفعيل (مرة واحدة فقط)</h3>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>افتح مجلد المشروع <code class="bg-white dark:bg-gray-800 px-1">C:\xampp\htdocs\whatsapp-local-system\scripts</code>.</li>
                                        <li>انقر نقراً مزدوجاً على <code class="bg-white dark:bg-gray-800 px-1">Install-AutoStart.bat</code>.</li>
                                        <li>سيطلب صلاحيات المسؤول (Administrator) تلقائياً — اضغط "نعم".</li>
                                        <li>انتظر حتى تظهر رسالة "تم الإعداد بنجاح!" ثم اضغط أي مفتاح لإغلاق النافذة.</li>
                                    </ol>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2">للإزالة</h3>
                                    <p class="text-sm">شغّل <code class="bg-white dark:bg-gray-800 px-1">Uninstall-AutoStart.bat</code> من نفس المجلد بنفس الطريقة — يزيل خدمة Apache ومهمّتي الطابور والمجدول.</p>
                                </div>
                            </div>

                            <div class="mt-4 bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border-r-4 border-amber-400">
                                <p class="text-sm font-semibold mb-1">تنبيهات مهمة جداً:</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li>بعد هذا الإعداد، <strong>لا تستخدم</strong> أزرار (تشغيل/إيقاف/إعادة تشغيل الخدمات) في لوحة تحكم النظام — أصبحت العمليات مُدارة عبر Task Scheduler مباشرة، واستخدام الطريقتين معاً قد يشغّل عاملين مكررين لنفس المهمة.</li>
                                    <li>عامل الطابور تحديداً يحتاج أن يكون هناك <strong>مستخدم مسجّل دخوله فعلياً</strong> على الجهاز (وليس فقط الجهاز مُشغَّلاً) — هذا مطلوب تقنياً لأن الطباعة الصامتة عبر SumatraPDF تحتاج جلسة تفاعلية حقيقية ولا تعمل بشكل موثوق تحت حساب النظام الخلفي (SYSTEM). لجهاز مكتب يبقى مسجَّل الدخول باستمرار، هذا غير ملحوظ عملياً.</li>
                                    <li>أي تعديل على ملفات الكود (PHP) بعد هذا الإعداد <strong>يتطلب إعادة تشغيل عامل الطابور</strong> يدوياً من لوحة التحكم (زر "إعادة تشغيل قائمة الانتظار") حتى تُطبَّق التعديلات — العامل المسجَّل يحتفظ بنسخة الكود من وقت آخر تشغيل له فقط.</li>
                                </ul>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">9. التشغيل على خوادم الاستضافة (Hosting)</h2>
                            <p class="mb-4">عند رفع المشروع على استضافة (مثل VPS أو Shared Hosting) بدلاً من جهاز Windows محلي، يجب إعداد التالي:</p>

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
                            <p class="text-sm mt-3 text-gray-500">ملاحظة: ميزة الطباعة الذكية (SumatraPDF) مخصصة لبيئة Windows فقط، ولا تعمل على استضافة Linux — تُستخدم الاستضافة عادة للنظام المركزي وليس النظام المحلي الذي يحتاج طابعة فعلية متصلة بنفس الجهاز.</p>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">10. مزامنة جهات الاتصال يدوياً (Contacts Sync)</h2>
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
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">11. ملاحظات هامة</h2>
                            <ul class="list-disc list-inside space-y-2 mr-4 text-gray-700 dark:text-gray-300">
                                <li>النظام يعتمد على ملفات يتم وضعها في مجلد <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">C:\PrintMonitor</code> ليقوم بمعالجتها وإرسالها عبر الواتساب — راجع القسم 6 لشرح كامل.</li>
                                <li>تأكد من أن خدمة "الجدولة" (Scheduler) وعامل الطابور (Queue Worker) يعملان في الخلفية باستمرار لضمان استمرارية الإرسال والطباعة — الطريقة الأضمن هي التشغيل التلقائي بالقسم 8 بدل التشغيل اليدوي.</li>
                                <li>يمكنك متابعة حالة الربط مع الخدمة المركزية من خلال لوحة التحكم وصفحة "صحة النظام".</li>
                                <li><strong>بعد أي تعديل على ملفات الكود</strong>، أعد تشغيل عامل الطابور من لوحة التحكم — لا يقرأ التعديلات تلقائياً أثناء عمله.</li>
                                <li>SumatraPDF مطلوب لتشغيل ميزة الطباعة الذكية، وTesseract OCR اختياري ويُستخدم فقط كحل أخير لقراءة الملفات الممسوحة ضوئياً أو الصور — راجع القسم 5.</li>
                            </ul>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
