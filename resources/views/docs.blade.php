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
                            <p class="mb-4">عند وصول ملف PDF أو صورة (jpg, png, gif, bmp, tiff) أو مستند أوفيس (doc, docx, xls, xlsx, ppt, pptx) عبر واتساب (أو وضعه في مجلد المراقبة) يحتوي على كلمة طباعة (مثل "اطبع"، "print")، يعمل النظام تلقائياً وفق الخطوات التالية:</p>
                            <p class="text-xs text-gray-500 mb-4">ملاحظة: ملفات Word/Excel/PowerPoint تُحوَّل تلقائياً إلى PDF عبر LibreOffice (راجع القسم 5) قبل طباعتها — بلا أي إجراء إضافي مطلوب منك. الامتدادات المدعومة قابلة للتخصيص عبر <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">PRINTABLE_EXTENSIONS</code> في <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">.env</code>.</p>

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

                            <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <h3 class="font-bold mb-3 text-[#f53003]">طباعة الصور — تجهيز خاص قبل الطباعة</h3>
                                <p class="text-sm mb-3">صور واتساب (JPG/PNG...) تصل دائماً <strong>بلا أي بيانات دقة (DPI)</strong> مضمَّنة في الملف. لو أُرسلت للطابعة كما هي، يحسب SumatraPDF حجم "صفحة" الطباعة مباشرة من أبعاد الصورة بالبكسل، فينتج مقاساً غير قياسي (custom) لا يطابق أي درج ورق — إما لا يظهر محتوى مطبوع إطلاقاً رغم نجاح الأمر برمجياً، أو تظهر رسالة "الورق غير موجود" رغم وجود ورق فعلياً. لذلك يضع النظام تلقائياً كل صورة داخل صفحة قياسية كاملة (خلفية بيضاء، الصورة مُصغَّرة عند الحاجة بمنتصف الصفحة مع الحفاظ على أبعادها) قبل إرسالها للطباعة.</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><code class="bg-white dark:bg-gray-800 px-1">PRINT_IMAGE_PAGE_SIZE</code> (في <code class="bg-white dark:bg-gray-800 px-1">.env</code>، الافتراضي <code class="bg-white dark:bg-gray-800 px-1">a4</code>): مقاس الصفحة (<code class="bg-white dark:bg-gray-800 px-1">a4</code> أو <code class="bg-white dark:bg-gray-800 px-1">letter</code>) — <strong>يجب أن يطابق مقاس الورق الفعلي المُحمَّل في الطابعة</strong>، وإلا ستظهر رسالة "الورق غير موجود" من جديد.</li>
                                    <li><code class="bg-white dark:bg-gray-800 px-1">PRINT_IMAGE_DPI</code> (الافتراضي <code class="bg-white dark:bg-gray-800 px-1">200</code>): الدقة المفروضة على الصورة قبل الطباعة. قيمة متوازنة بين الوضوح وحجم الملف — لا حاجة لتغييرها عادة.</li>
                                </ul>
                                <p class="text-xs text-gray-500 mt-2">النسخ المحلية المُجهَّزة تُحفظ في <code class="bg-white dark:bg-gray-800 px-1">storage/app/private/print_jobs</code>، وتُحذف تلقائياً بعد نفس مدة <code class="bg-white dark:bg-gray-800 px-1">FILE_AUTO_DELETE_DAYS</code> المستخدمة لتنظيف مجلد المراقبة (راجع القسم 6) — عبر نفس الأمر المجدول <code class="bg-white dark:bg-gray-800 px-1">files:clean-old</code>، بلا حاجة لإعداد منفصل.</p>
                            </div>

                            <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <h3 class="font-bold mb-3 text-[#f53003]">كيف يختار النظام الطابعة المناسبة؟ (قواعد التوجيه)</h3>
                                <p class="text-sm mb-3">من صفحة "الطابعات وقواعد التوجيه"، يمكنك إضافة قواعد تحدّد أي طابعة تُستخدم حسب: رقم جوال محدد، بادئة رقم، كلمة مفتاحية في نص الرسالة، أو امتداد الملف. الترتيب: القواعد تُفحص حسب الأولوية (الأصغر أولاً)، وأول قاعدة مفعّلة تُطابق تفوز. <strong>يمكن وضع أكثر من قيمة في نفس القاعدة مفصولة بفاصلة</strong> — لأي نوع مطابقة، وليس فقط الكلمات المفتاحية. مثال لقاعدة أرقام جوال: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">966501111111,966502222222,966503333333</code> تُطابق أياً من هذه الأرقام الثلاثة.</p>

                                <div class="bg-red-50 dark:bg-red-900/20 border-r-4 border-red-400 p-4 rounded-lg mb-3">
                                    <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">⚠️ تنبيه مهم جداً بخصوص "الطابعة الافتراضية" (Default)</p>
                                    <p class="text-sm">إذا لم تُطابق أي قاعدة الرسالة الواردة، يستخدم النظام <strong>الطابعة المُحدَّدة كافتراضية</strong> (إن وُجدت) لطباعة الملف — <strong>بصرف النظر تماماً عن وجود أي كلمة طباعة في الرسالة من عدمه</strong>. بمعنى آخر: أي طابعة افتراضية تطبع كل ملف PDF وارد تلقائياً، حتى لو لم يكتب العميل "اطبع" إطلاقاً. لهذا: لا تُحدّد طابعة افتراضية إلا إذا كنت تريد فعلاً طباعة كل PDF وارد بلا استثناء. إذا كنت تعتمد على كلمات مفتاحية للتحكم بما يُطبع، اترك كل الطابعات "غير افتراضية" واعتمد فقط على قواعد الكلمات/الأرقام.</p>
                                </div>

                                <h4 class="font-bold text-sm mb-2">سيناريو: النظام المحلي يعمل في عدة فروع</h4>
                                <p class="text-sm mb-2">إذا كانت عدة فروع تشترك في نفس رقم واتساب الشركة المركزي (الوضع الافتراضي حالياً، بلا توجيه تلقائي للفرع الصحيح من النظام المركزي)، فإن كل رسالة واردة تصل لكل الأنظمة المحلية المسجَّلة في آن واحد. لتفادي طباعة نفس الملف في كل الفروع معاً:</p>
                                <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                    <li>اجعل كلمة الطباعة <strong>مختلفة وفريدة لكل فرع</strong> (وليست كلمة عامة مشتركة مثل "اطبع" وحدها) — مثلاً فرع الرياض: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">طباعة_رياض,اطبع_رياض</code>، وفرع جدة: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">طباعة_جدة,اطبع_جدة</code>.</li>
                                    <li><strong>لا تُحدّد طابعة افتراضية في أي فرع</strong> (راجع التنبيه أعلاه) — وإلا سيطبع كل فرع أي ملف بلا شرط، بصرف النظر عن الكلمة.</li>
                                    <li>أبلغ عملاء كل فرع بالكلمة الصحيحة الخاصة به (لافتة، رسالة ترحيب تلقائية، إلخ) بما أن العميل هو من يحدد الفرع فعلياً بالكلمة التي يكتبها.</li>
                                    <li>بديل/تكميل أكثر أماناً لعملاء معروفين: أضف قاعدة <code class="bg-white dark:bg-gray-800 px-1">رقم جوال محدد</code> بأرقام هؤلاء العملاء (مفصولة بفاصلة كما سبق) لكل فرع، فتُوجَّه رسائلهم لطابعة فرعهم تلقائياً دون الحاجة لكتابة أي كلمة.</li>
                                </ol>
                                <p class="text-xs text-gray-500 mt-2">الحل الأشمل معمارياً (يحتاج تعديلاً في النظام المركزي، غير مطبَّق حالياً) هو ربط كل فرع برقم واتساب مستقل خاص به. طريقة الكلمات المفتاحية أعلاه بديل عملي لا يحتاج أي تعديل، ويعمل بالإعدادات الحالية مباشرة.</p>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">5. البرامج الخارجية المطلوبة</h2>
                            <p class="mb-4">النظام لا يقوم بالطباعة أو قراءة الصور/المستندات الممسوحة ضوئياً بنفسه — يعتمد على برامج خارجية مجانية يجب تثبيتها على نفس الجهاز:</p>

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

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-indigo-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">LibreOffice — لطباعة ملفات Word/Excel/PowerPoint</h3>
                                    <p class="text-sm mb-2">SumatraPDF لا يفهم صيغ الأوفيس مباشرة، لذا يُستخدم LibreOffice بوضع "بصمت" (headless، بلا فتح أي نافذة) لتحويل الملف إلى PDF أولاً، ثم يُطبع بنفس مسار PDF المعتاد. اختياري — بدونه تبقى طباعة PDF والصور تعمل بشكل طبيعي، وتفشل فقط طباعة ملفات الأوفيس تحديداً.</p>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>نزّل ونثبّت النسخة الكاملة (وليست Portable) من الموقع الرسمي: <a href="https://www.libreoffice.org/download/download/" class="text-blue-500 underline" target="_blank">libreoffice.org</a></li>
                                        <li>المسار الافتراضي بعد التثبيت: <code class="bg-white dark:bg-gray-800 px-1">C:/Program Files/LibreOffice/program/soffice.exe</code> — يطابق قيمة <code class="bg-white dark:bg-gray-800 px-1">LIBREOFFICE_PATH</code> الافتراضية في <code class="bg-white dark:bg-gray-800 px-1">.env</code>، فلا حاجة لتعديل شيء إن ثبَّتّه بالمسار الافتراضي.</li>
                                        <li>إن اخترت مساراً مختلفاً، حدّثه في <code class="bg-white dark:bg-gray-800 px-1">LIBREOFFICE_PATH</code> بملف <code class="bg-white dark:bg-gray-800 px-1">.env</code>.</li>
                                    </ol>
                                    <p class="text-xs text-gray-500 mt-2">ملاحظة أداء: أول تحويل بعد كل إعادة تشغيل لعامل الطابور (Queue Worker) قد يستغرق دقيقة أو أكثر (تهيئة داخلية لمرة واحدة)، والتحويلات التالية أسرع من ذلك عادة. مهلة الانتظار قابلة للتعديل عبر <code class="bg-white dark:bg-gray-800 px-1">OFFICE_CONVERSION_TIMEOUT_SECONDS</code> (الافتراضي 120 ثانية).</p>
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

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-purple-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">Ghostscript — لتحويل صفحة PDF إلى صورة قبل قراءتها بـ OCR</h3>
                                    <p class="text-sm mb-2">مكمّل لـ Tesseract OCR أعلاه: عندما تكون طبقة نص ملف PDF تالفة تماماً (عطل ترميز خط شائع في بعض الملفات الممسوحة ضوئياً) أو غير موجودة أصلاً، يُستخدم Ghostscript لتحويل الصفحة الأولى من الملف إلى صورة PNG، ثم تُقرأ هذه الصورة عبر Tesseract كحل أخير. اختياري تماماً مثل Tesseract — بدونه يعمل النظام بشكل طبيعي، لكن هذه الحالة المحددة (PDF بطبقة نص تالفة) لن تُحل تلقائياً.</p>
                                    <ol class="list-decimal list-inside text-sm space-y-1 mr-4">
                                        <li>نزّل المثبِّت لويندوز (64-bit) من الموقع الرسمي: <a href="https://ghostscript.com/releases/gsdnld.html" class="text-blue-500 underline" target="_blank">ghostscript.com/releases</a> أو من <a href="https://github.com/ArtifexSoftware/ghostpdl-downloads/releases" class="text-blue-500 underline" target="_blank">GitHub الرسمي</a>.</li>
                                        <li>ثبّته بالإعدادات الافتراضية (المسار المعتاد: <code class="bg-white dark:bg-gray-800 px-1">C:/Program Files/gs/gsX.XX.X/bin/gswin64c.exe</code>، حيث X.XX.X رقم الإصدار).</li>
                                        <li>أضف السطر التالي في ملف <code class="bg-white dark:bg-gray-800 px-1">.env</code> بالمسار الفعلي بعد التثبيت لديك:
                                            <pre class="bg-gray-900 text-gray-100 p-2 rounded mt-1 text-xs dir-ltr">GHOSTSCRIPT_BIN_PATH="C:/Program Files/gs/gs10.07.1/bin/gswin64c.exe"</pre>
                                        </li>
                                    </ol>
                                    <p class="text-xs text-gray-500 mt-2">للتحقق من التثبيت الصحيح: نفّذ <code class="bg-white dark:bg-gray-800 px-1">"C:\Program Files\gs\gsX.XX.X\bin\gswin64c.exe" --version</code> في Command Prompt — يجب أن يظهر رقم الإصدار بلا أخطاء.</p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                                ⚠️ بعد تثبيت أو تعديل مسار أي من هذه البرامج في <code>.env</code>، يجب <strong>إعادة تشغيل قائمة الانتظار (Queue Worker)</strong> من لوحة التحكم حتى يقرأ الإعداد الجديد.
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

                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg mt-4">
                                <h3 class="font-bold mb-2 text-[#f53003]">الحذف التلقائي للملفات القديمة</h3>
                                <p class="text-sm mb-2">حتى لا تمتلئ هذه المجلدات بمرور الوقت، يحذف النظام تلقائياً كل يوم الملفات الأقدم من مدة معينة من المجلدات الفرعية الأربعة (<code class="bg-white dark:bg-gray-800 px-1">processing</code>، <code class="bg-white dark:bg-gray-800 px-1">review</code>، <code class="bg-white dark:bg-gray-800 px-1">archive</code>، <code class="bg-white dark:bg-gray-800 px-1">failed</code>) — وليس مجلد الانتظار الرئيسي (جذر المجلد) الذي يحتوي ملفات لم تُعالَج بعد. <strong>ونفس المدة تُطبَّق أيضاً على النسخ المحلية المؤقتة لملفات الطباعة</strong> في <code class="bg-white dark:bg-gray-800 px-1">storage/app/private/print_jobs</code> (راجع القسم 4).</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><strong>لتحديد المدة:</strong> من صفحة الإعدادات → <code class="bg-white dark:bg-gray-800 px-1">Auto Delete Days (FILE_AUTO_DELETE_DAYS)</code> — عدد الأيام كما تريد، بلا حاجة لتعديل أي ملف. القيمة الحالية الافتراضية: 3 أيام.</li>
                                    <li>ضع القيمة <code class="bg-white dark:bg-gray-800 px-1">0</code> لتعطيل الحذف التلقائي بالكامل والاحتفاظ بكل الملفات إلى الأبد.</li>
                                    <li>ملفات مجلد <code class="bg-white dark:bg-gray-800 px-1">review</code> التي تنتهي مهلتها بلا مراجعة يدوية تُحذف أيضاً، وتتحول حالة رسالتها تلقائياً إلى "فشلت" مع توضيح السبب (انتهاء المهلة)، بدل بقائها معلّقة في صفحة متابعة الإرسال إلى ما لا نهاية.</li>
                                    <li>الأمر المسؤول عن هذا: <code class="bg-white dark:bg-gray-800 px-1">php artisan files:clean-old</code> — يعمل تلقائياً ضمن الجدولة اليومية (لا حاجة لتشغيله يدوياً).</li>
                                </ul>
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
                            <p class="mb-4">بدل تشغيل الأوامر السابقة يدوياً في نوافذ Terminal تبقى مفتوحة (وتتوقف عند إغلاقها أو إعادة تشغيل الجهاز)، يوفّر النظام سكربتات جاهزة في مجلد <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">scripts/</code> تُعِدّ تشغيلاً تلقائياً كاملاً يعمل من نفسه بعد كل إقلاع للجهاز — الموقع، قاعدة البيانات، الطباعة، والجدولة، بلا أي تدخل يدوي. هذه هي الطريقة الموصى بها لجهاز العمل الفعلي، وهي نفسها المطلوبة لإعداد النظام على جهاز جديد.</p>

                            <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border-r-4 border-indigo-400 mb-4">
                                <p class="text-sm font-semibold mb-2">كيف يعمل؟ (5 مكوّنات مستقلة، كل منها يُسجَّل بطريقة مختلفة حسب طبيعته)</p>
                                <ul class="list-disc list-inside text-sm space-y-2 mr-4">
                                    <li>
                                        <strong>Apache (تقديم الموقع):</strong> يُنسخ من تثبيت XAMPP إلى نسخة معزولة خاصة بهذا المشروع فقط (<code class="bg-white dark:bg-gray-800 px-1">scripts/apache-standalone</code>)، بمنفذ خاص بها (من <code class="bg-white dark:bg-gray-800 px-1">APP_URL</code>)، دون أي تعديل على تثبيت XAMPP الأصلي أو أي مشروع آخر على نفس الجهاز — ثم تُسجَّل كخدمة Windows (<code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalApache</code>) تعمل تلقائياً عند الإقلاع، <strong>مع إعداد استرداد تلقائي عند الانهيار</strong> (<code class="bg-white dark:bg-gray-800 px-1" dir="ltr">sc.exe failure</code> — 3 محاولات إعادة تشغيل، كل 60 ثانية) — بخلاف سلوك خدمات Windows الافتراضي (لا تُعاد تلقائياً أبداً بدون هذا الإعداد الصريح).
                                    </li>
                                    <li>
                                        <strong>MySQL (قاعدة البيانات):</strong> بخلاف Apache، هنا يُسجَّل نفس تثبيت MySQL الموجود أصلاً ضمن XAMPP (بنفس بياناته الحالية، بلا نسخ أو تعديل) كخدمة Windows (<code class="bg-white dark:bg-gray-800 px-1">MySQL_XAMPP</code>) بنفس إعداد الاسترداد التلقائي أعلاه. هذه الخطوة ضرورية لأن XAMPP <strong>لا يُسجِّل MySQL كخدمة تلقائية افتراضياً</strong> — بدونها يفشل الموقع بالكامل (خطأ HTTP 500، "connection refused") بعد كل إعادة تشغيل للجهاز رغم عمل Apache نفسه بنجاح، لأن كل صفحة تحتاج قاعدة البيانات.
                                    </li>
                                    <li>
                                        <strong>عامل الطابور (<code class="bg-white dark:bg-gray-800 px-1">queue:work</code>):</strong> يُسجَّل كمهمة في "جدولة المهام" (Task Scheduler) باسم <code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalSystem-QueueWorker</code>، بمحفّزين: "عند الإقلاع" و"عند تسجيل الدخول" (الثاني أكثر موثوقية لهذه المهمة تحديداً — راجع التنبيه أدناه)، مع إعادة تشغيل تلقائية عند التعطل.
                                    </li>
                                    <li>
                                        <strong>المجدول (<code class="bg-white dark:bg-gray-800 px-1">schedule:work</code>):</strong> نفس فكرة عامل الطابور، بمهمة منفصلة باسم <code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalSystem-Scheduler</code>، وهو المسؤول عن تشغيل كل المهام الدورية (مزامنة، فحص الطابعات، الحذف التلقائي...) — راجع القسم 7.
                                    </li>
                                    <li>
                                        <strong>السحب التلقائي للتحديثات (<code class="bg-white dark:bg-gray-800 px-1">auto-update.ps1</code>):</strong> مهمة منفصلة باسم <code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalSystem-AutoUpdate</code> تعمل تحت حساب SYSTEM (لا تحتاج جلسة تفاعلية) كل 10 دقائق — تفحص فرع <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">main</code> على GitHub، وإن وُجد تحديث جديد تسحبه (<code class="bg-white dark:bg-gray-800 px-1">git pull</code>) وتُطبِّق <code class="bg-white dark:bg-gray-800 px-1">composer install</code> و<code class="bg-white dark:bg-gray-800 px-1">npm run build</code> و<code class="bg-white dark:bg-gray-800 px-1">migrate</code> تلقائياً، ثم تُعيد تشغيل عامل الطابور ليطبّق الكود الجديد. راجع الصندوق أدناه للتفاصيل الكاملة.
                                    </li>
                                </ul>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border-r-4 border-purple-400 mb-4">
                                <h3 class="text-lg font-bold mb-2 text-[#f53003]">السحب التلقائي للتحديثات من GitHub</h3>
                                <p class="text-sm mb-2">بمجرد إتمام إعداد التشغيل التلقائي (القسم أدناه)، يفحص النظام فرع <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">main</code> على GitHub كل 10 دقائق تلقائياً — أي تحديث يُدفع (<code class="bg-white dark:bg-gray-800 px-1">push</code>) لهذا الفرع يصل لهذا الجهاز ويُطبَّق بالكامل خلال 10 دقائق كحد أقصى، بلا أي تدخل يدوي.</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><strong>الأمان:</strong> لا يلمس السكربت أي تعديلات محلية غير مرفوعة على الجهاز — إن وجد الشجرة المحلية غير نظيفة (<code class="bg-white dark:bg-gray-800 px-1" dir="ltr">git status</code> غير فارغ)، يتوقف فوراً ويُسجِّل تحذيراً بدل تجاهل التعديلات أو حذفها.</li>
                                    <li><strong>نسخة احتياطية قبل كل migration:</strong> يأخذ السكربت نسخة احتياطية سريعة من قاعدة البيانات (<code class="bg-white dark:bg-gray-800 px-1" dir="ltr">mysqldump</code>) في <code class="bg-white dark:bg-gray-800 px-1">storage/app/private/db_backups</code> <strong>قبل</strong> تطبيق أي migration جديد مباشرة — إن فشل النسخ الاحتياطي نفسه، يتوقف السكربت عمداً قبل migrate (بدل المخاطرة بتطبيق تحديث خطر بلا أي نسخة للتراجع إليها). يُحتفَظ بآخر 10 نسخ فقط تلقائياً.</li>
                                    <li><strong>تنبيه فوري عند الفشل:</strong> أي فشل في السحب أو التحديث يُرسل تلقائياً كرسالة واتساب لرقم <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> (نفس رقم تنبيهات صحة الطابعات)، بدل بقاء الفشل مخفياً في ملف سجل لا يراجعه أحد.</li>
                                    <li><strong>السجل:</strong> كل عملية تحديث (نجحت أم فشلت) تُسجَّل بالتفصيل في <code class="bg-white dark:bg-gray-800 px-1">storage/logs/auto-update.log</code> — راجعه للتأكد من آخر تحديث تم تطبيقه أو لمعرفة سبب أي فشل.</li>
                                    <li><strong>تنبيه مهم:</strong> بما أن أي <code class="bg-white dark:bg-gray-800 px-1">push</code> على <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">main</code> يصل مباشرة لجهاز الإنتاج الحقيقي بلا مراجعة بشرية، <strong>لا تدفع كوداً غير مختبر على هذا الفرع</strong> — اختبره على فرع آخر أولاً إن كان تجريبياً.</li>
                                    <li>لتعطيل هذه الميزة: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">Unregister-ScheduledTask -TaskName "WhatsAppLocalSystem-AutoUpdate"</code> من PowerShell كمسؤول.</li>
                                </ul>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2">للتفعيل على جهاز جديد — بما فيه التحميل من GitHub (مرة واحدة فقط)</h3>
                                    <p class="text-sm mb-2">المتطلبات: XAMPP مثبَّت بمساره الافتراضي <code class="bg-white dark:bg-gray-800 px-1">C:\xampp</code> فقط (يحتوي PHP وApache وMySQL) — <strong>لا حاجة لتثبيت Node.js أو Composer يدوياً</strong>، السكربتات التالية تُثبِّتهما تلقائياً. نفّذ الملفات الأربعة التالية من مجلد <code class="bg-white dark:bg-gray-800 px-1">scripts</code> <strong>بالترتيب الرقمي</strong> (نفس الترتيب موثّق أيضاً في <code class="bg-white dark:bg-gray-800 px-1">scripts/README-Installation.txt</code>):</p>
                                    <ol class="list-decimal list-inside text-sm space-y-2 mr-4">
                                        <li>
                                            <code class="bg-white dark:bg-gray-800 px-1">01-Install-Prerequisites.bat</code> (كليك يمين ← Run as Administrator): يضيف مسار PHP للنظام، ويثبّت Node.js وComposer، بالإضافة لـ SumatraPDF وTesseract OCR وLibreOffice (الثلاثة الأخيرة عبر winget، ويتخطّى أي برنامج مثبَّت مسبقاً بأمان).
                                            <br><strong class="text-amber-700 dark:text-amber-400">مهم جداً:</strong> بعد ظهور رسالة النجاح، <strong>أغلق نافذة CMD السوداء</strong> قبل المتابعة — ضروري حتى يتعرّف Windows على مسارات Node وComposer الجديدة في أي نافذة تُفتح لاحقاً.
                                        </li>
                                        <li>
                                            <code class="bg-white dark:bg-gray-800 px-1">02-Setup-Project.bat</code> (تشغيل عادي بنقرتين، بلا صلاحيات مسؤول): يجهّز <code class="bg-white dark:bg-gray-800 px-1">.env</code> (نسخ من <code class="bg-white dark:bg-gray-800 px-1">.env.example</code> إن لم يكن موجوداً)، يشغّل <code class="bg-white dark:bg-gray-800 px-1">composer install</code>، يولّد <code class="bg-white dark:bg-gray-800 px-1">APP_KEY</code>، ينشئ قاعدة البيانات (<code class="bg-white dark:bg-gray-800 px-1">whatsapp_local</code>) وينفّذ <code class="bg-white dark:bg-gray-800 px-1">migrate</code>، يربط مجلد التخزين العام (<code class="bg-white dark:bg-gray-800 px-1">storage:link</code> — ضروري لعرض مرفقات واتساب بشكل صحيح)، ثم يبني الواجهات (<code class="bg-white dark:bg-gray-800 px-1">npm install &amp;&amp; npm run build</code>).
                                            <br><strong>تأكد أن خدمة MySQL تعمل</strong> (من لوحة تحكم XAMPP) قبل تشغيل هذا الملف، وإلا فشلت خطوة إنشاء قاعدة البيانات.
                                        </li>
                                        <li><code class="bg-white dark:bg-gray-800 px-1">03-Install-AutoStart.bat</code> (كليك يمين ← Run as Administrator): يُعِدّ التشغيل التلقائي الكامل (Apache + MySQL كخدمتي Windows، وعامل الطابور والمجدول كمهام مجدولة) — نفس السكربت الموصوف بالتفصيل في بقية هذا القسم.</li>
                                        <li>انتظر حتى تظهر رسالة "تم الإعداد بنجاح!" في الخطوة الأخيرة، ثم افتح الرابط الظاهر (مثال: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">http://localhost:8006</code>) للتأكد أن الموقع يعمل فعلياً.</li>
                                    </ol>
                                    <p class="text-xs text-gray-500 mt-2"><strong>Ghostscript وحده</strong> يبقى تثبيته يدوياً (لا توجد له حزمة winget موثوقة؛ حالة نادرة أصلاً — راجع القسم 5).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2">كيف أتأكد أن كل شيء يعمل بعد إعادة تشغيل الجهاز؟</h3>
                                    <p class="text-sm mb-2">افتح PowerShell كمسؤول ونفّذ الأوامر التالية — يجب أن تظهر كل الحالات <code class="bg-white dark:bg-gray-800 px-1">Running</code>:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr text-xs">Get-Service WhatsAppLocalApache, MySQL_XAMPP | Select Name, Status
Get-ScheduledTask WhatsAppLocalSystem-QueueWorker, WhatsAppLocalSystem-Scheduler | Select TaskName, State</pre>
                                    <p class="text-xs text-gray-500 mt-2">إن ظهرت مهمة بحالة <code class="bg-white dark:bg-gray-800 px-1">Ready</code> بدل <code class="bg-white dark:bg-gray-800 px-1">Running</code>، شغّلها يدوياً مرة واحدة بـ <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">Start-ScheduledTask -TaskName "الاسم"</code> — راجع التنبيه أدناه عن سبب حدوث هذا أحياناً.</p>
                                    <p class="text-xs text-gray-500 mt-2">ملاحظة: مهمة <code class="bg-white dark:bg-gray-800 px-1">WhatsAppLocalSystem-AutoUpdate</code> تختلف عن البقية — تعمل لثوانٍ فقط كل 10 دقائق ثم تعود لحالة <code class="bg-white dark:bg-gray-800 px-1">Ready</code> بين كل تشغيل (طبيعي وليس عطلاً)، بدل البقاء <code class="bg-white dark:bg-gray-800 px-1">Running</code> باستمرار كعامل الطابور والمجدول.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2">للإزالة</h3>
                                    <p class="text-sm">شغّل <code class="bg-white dark:bg-gray-800 px-1">04-Uninstall-AutoStart.bat</code> من نفس المجلد بنفس الطريقة — يزيل خدمة Apache ومهمّتي الطابور والمجدول. <strong>لا يزيل خدمة MySQL عمداً</strong> (قد تُستخدم من مشاريع أخرى على نفس الجهاز) — أزلها يدوياً من <code class="bg-white dark:bg-gray-800 px-1">services.msc</code> إن أردت فعلاً وتأكدت أن لا شيء آخر يعتمد عليها.</p>
                                </div>
                            </div>

                            <div class="mt-4 bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border-r-4 border-amber-400">
                                <p class="text-sm font-semibold mb-1">تنبيهات مهمة جداً:</p>
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li>بعد هذا الإعداد، <strong>لا تستخدم</strong> أزرار (تشغيل/إيقاف/إعادة تشغيل الخدمات) في لوحة تحكم النظام أو لوحة تحكم XAMPP — أصبحت العمليات مُدارة عبر Windows Services / Task Scheduler مباشرة، واستخدام الطريقتين معاً قد يشغّل عاملين مكررين لنفس المهمة.</li>
                                    <li>عامل الطابور تحديداً يحتاج أن يكون هناك <strong>مستخدم مسجّل دخوله فعلياً</strong> على الجهاز (وليس فقط الجهاز مُشغَّلاً) — هذا مطلوب تقنياً لأن الطباعة الصامتة عبر SumatraPDF تحتاج جلسة تفاعلية حقيقية ولا تعمل بشكل موثوق تحت حساب النظام الخلفي (SYSTEM). لجهاز مكتب يبقى مسجَّل الدخول باستمرار، هذا غير ملحوظ عملياً.</li>
                                    <li><strong>لوحظ فعلياً:</strong> محفّز "عند الإقلاع" وحده لمهمة عامل الطابور قد يفشل بصمت أحياناً إذا حاول العمل قبل اكتمال تسجيل الدخول الفعلي على سطح المكتب (Windows لا يعيد المحاولة تلقائياً في هذه الحالة رغم إعداد إعادة التشغيل، لأن المهمة لم "تبدأ" أصلاً من منظوره). لذلك يُضاف أيضاً محفّز "عند تسجيل الدخول" لنفس المستخدم كطبقة أمان — إن لاحظت رغم ذلك أن المهمة بقيت متوقفة بعد إقلاع نادر، شغّلها يدوياً مرة واحدة كما في الفقرة أعلاه.</li>
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

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">12. ربط النظام بالإنترنت مجاناً (Cloudflare)</h2>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-bold mb-2 text-[#f53003]">الطريقة الأولى: الاتصال السريع للتجربة (بدون حساب)</h3>
                                <p class="mb-2">قم بتشغيل الملف التالي الموجود في مجلد السكربتات:</p>
                                <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr mb-4">scripts\05-Start-Quick-Tunnel.bat</pre>
                                <p class="text-sm mb-6">سيعطيك رابطاً عشوائياً يمكنك استخدامه فوراً للوصول للنظام من أي مكان في العالم (يتغير هذا الرابط عند إعادة تشغيل الكمبيوتر).</p>
                                
                                <h3 class="text-lg font-bold mb-2 text-[#f53003]">الطريقة الثانية: الاتصال الدائم (يتطلب حساب مجاني)</h3>
                                <p class="mb-2">للحصول على رابط احترافي ثابت يعمل دائماً في الخلفية كخدمة ويندوز:</p>
                                <ul class="list-decimal list-inside space-y-2 mr-4 text-gray-700 dark:text-gray-300">
                                    <li>قم بإنشاء حساب في منصة <strong>Cloudflare Zero Trust</strong> المجانية.</li>
                                    <li>انتقل إلى <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">Networks</code> &gt; <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">Tunnels</code> واضغط على <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">Create a tunnel</code> (اختر نوع Cloudflared).</li>
                                    <li>في صفحة التثبيت (Windows)، ستجد أمراً يحتوي على الرمز الخاص بك (Token).</li>
                                    <li>افتح موجه الأوامر (CMD) <strong>كمسؤول</strong> في مجلد <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">scripts</code>، وقم بتشغيل الأمر:</li>
                                </ul>
                                <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr mt-2 mb-2">cloudflared.exe service install YOUR_TOKEN_HERE</pre>
                                <p class="text-sm">بعدها من موقع Cloudflare، قم بتوجيه النفق (Service) إلى <code class="bg-gray-100 dark:bg-gray-600 px-1 rounded">http://localhost:8006</code> وسيعمل الرابط بشكل دائم وثابت!</p>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">13. تحديثات أخيرة: موافقة الطباعة والإرسال + مجلد طباعة مستقل لكل طابعة</h2>
                            <p class="mb-4">ثلاث إضافات مترابطة، هدفها إعطاء المسؤول تحكماً كاملاً في أي طباعة/إرسال يحدث تلقائياً بلا مراجعة بشرية، مع إبقاء الوضع الحالي (تلقائي بالكامل) كافتراضي بلا أي تغيير في السلوك ما لم تُفعّلها بنفسك.</p>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-purple-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">1. وضع الطباعة لكل طابعة: تلقائي أو يتطلب موافقة</h3>
                                    <p class="text-sm mb-2">من صفحة "الطابعات"، لكل طابعة عمود جديد "وضع الطباعة" بقيمتين:</p>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li><strong>تلقائي</strong> (الافتراضي، نفس السلوك القديم): يُطبع أي ملف مطابق فوراً بلا أي تدخل.</li>
                                        <li><strong>يتطلب موافقة</strong>: أي طلب طباعة يُطابق هذه الطابعة (سواء عبر مرفق واتساب أو مجلد الطباعة المستقل، البند 3 أدناه) يُحجز بحالة "بانتظار الموافقة" في صفحة "سجل عمليات الطباعة"، ولا يُطبع إلا بعد موافقة صريحة.</li>
                                    </ul>
                                    <p class="text-sm mt-2">مفيد لطابعات تستهلك مستلزمات مكلفة (حبر ملون، ورق خاص) أو ذات صلاحية وصول حساسة تريد مراجعة كل طلب لها يدوياً.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-orange-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">2. الموافقة على طلبات الطباعة</h3>
                                    <p class="text-sm mb-2">عند حجز طلب طباعة بانتظار الموافقة، يصل إشعار واتساب فوري لرقم <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> (نفس رقم تنبيهات صحة الطابعات) بتفاصيل الطلب (رقم المهمة، المصدر، اسم الملف، الطابعة). الموافقة تتم بأحد طريقتين:</p>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li>زرّي "موافقة" / "رفض" في صفحة "سجل عمليات الطباعة" أمام أي مهمة بحالة "بانتظار الموافقة".</li>
                                        <li>الرد على رسالة الإشعار عبر واتساب من نفس رقم <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> بأحد النصوص التالية (النظام لا يدعم أزرار تفاعلية حالياً، فقط رد نصي عادي):
                                            <ul class="list-disc list-inside mr-6 mt-1 space-y-1">
                                                <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">وافق طباعة &lt;رقم المهمة&gt;</code> — تنفّذ الطباعة فوراً.</li>
                                                <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">رفض طباعة &lt;رقم المهمة&gt;</code> — تُلغى دون طباعة.</li>
                                                <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">ارسل لي الملف طباعة &lt;رقم المهمة&gt;</code> — يصلك الملف نفسه عبر واتساب لمعاينته قبل اتخاذ القرار (يعمل أيضاً بعد اكتمال الطباعة، ليس فقط قبل الموافقة).</li>
                                                <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">وافق الكل طباعة</code> — يوافق دفعة واحدة على كل مهام الطباعة المعلّقة حالياً (زر مطابق أيضاً في صفحة سجل عمليات الطباعة).</li>
                                            </ul>
                                        </li>
                                    </ul>
                                    <p class="text-xs text-gray-500 mt-2"><code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> يدعم أكثر من رقم مسؤول مفصولة بفواصل (مثال: <code class="bg-white dark:bg-gray-800 px-1" dir="ltr">966501111111,966502222222</code>) — كل رقم يستلم كل التنبيهات، وأي منهم يمكنه الرد بأوامر الموافقة.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-blue-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">3. مجلد طباعة مستقل لكل طابعة (بلا أي علاقة بواتساب)</h3>
                                    <p class="text-sm mb-2">بالإضافة لمجلد المراقبة الرئيسي (القسم 6، الذي يستخرج رقم جوال ويرسل الملف عبر واتساب)، توجد الآن مجلدات فرعية مخصصة للطباعة المحلية المباشرة فقط — بلا أي حاجة لرقم جوال أو إرسال واتساب إطلاقاً:</p>
                                    <pre class="bg-gray-900 text-gray-100 p-3 rounded dir-ltr mb-2">C:\PrintMonitor\print\&lt;اسم الطابعة&gt;\</pre>
                                    <p class="text-sm mb-2">تُنشأ هذه المجلدات تلقائياً لكل طابعة مفعّلة عند أول تشغيل لأمر <code class="bg-white dark:bg-gray-800 px-1">monitor:folder</code> (تجدها أيضاً موضّحة تحت اسم كل طابعة في صفحة "الطابعات"). أي ملف يوضع مباشرة في مجلد طابعة معيّنة يُطبع عليها تلقائياً أو ينتظر موافقة، حسب "وضع الطباعة" الخاص بها (البند 1 أعلاه) — بنفس آلية الموافقة والأوامر النصية في البند 2. لكل طابعة 4 مجلدات فرعية تُدار تلقائياً: الجذر (ضع الملف هنا)، <code class="bg-white dark:bg-gray-800 px-1">processing</code> (قيد المعالجة/الانتظار)، <code class="bg-white dark:bg-gray-800 px-1">archive</code> (طُبعت بنجاح)، <code class="bg-white dark:bg-gray-800 px-1">failed</code> (فشلت أو رُفضت).</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-green-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">4. موافقة على إرسال واتساب من مجلد المراقبة الرئيسي</h3>
                                    <p class="text-sm mb-2">بشكل مستقل تماماً عن موافقة الطباعة أعلاه، يمكن أيضاً فرض موافقة قبل إرسال أي ملف من مجلد المراقبة الرئيسي (<code class="bg-white dark:bg-gray-800 px-1">C:\PrintMonitor</code>) عبر واتساب — لا فقط الحالات منخفضة الثقة (القسم 4، البند 1) بل كل الملفات. يُفعَّل بضبط <code class="bg-white dark:bg-gray-800 px-1">MONITOR_FOLDER_REQUIRE_APPROVAL=true</code> في <code class="bg-white dark:bg-gray-800 px-1">.env</code> (الافتراضي <code class="bg-white dark:bg-gray-800 px-1">false</code> = إرسال تلقائي كالمعتاد).</p>
                                    <p class="text-sm mb-2">عند تفعيله، يصل إشعار واتساب لنفس رقم <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code>، والموافقة إما بزر من صفحة "متابعة الإرسال" أو برد نصي مشابه للبند 2 لكن بكلمة "ارسال" بدل "طباعة":</p>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">وافق ارسال &lt;رقم الرسالة&gt;</code></li>
                                        <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">رفض ارسال &lt;رقم الرسالة&gt;</code></li>
                                        <li><code class="bg-white dark:bg-gray-800 px-1" dir="ltr">ارسل لي الملف ارسال &lt;رقم الرسالة&gt;</code> — لمعاينته أولاً</li>
                                    </ul>
                                    <p class="text-xs text-gray-500 mt-2">كلمة النوع ("طباعة" أو "ارسال") إلزامية دوماً في كل الأوامر أعلاه — لأن ترقيم مهام الطباعة ورسائل مجلد المراقبة مستقلان تماماً (كلاهما يبدأ من 1)، فبدون تحديد النوع يصعب تمييز أي جدول يقصده الرقم.</p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">14. توزيع المحادثات على الموظفين + تنبيه التعيين</h2>
                            <p class="mb-4">إعداد ديناميكي (من صفحة "الإعدادات") يتحكم في كيفية تعيين موظف لكل محادثة واتساب جديدة تصل — بلا أي تعيين تلقائي افتراضياً (نفس السلوك القديم) ما لم تُفعّله بنفسك.</p>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-indigo-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">1. وضع التعيين (CONVERSATION_DISTRIBUTION_MODE)</h3>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li><strong>يدوي</strong> (الافتراضي): بلا تعيين تلقائي — يُعيَّن الموظف يدوياً من صفحة المحادثة، أو تلقائياً فقط عبر قاعدة أتمتة محددة (صفحة "الأتمتة") إن وُجدت.</li>
                                        <li><strong>تلقائي لمستخدمين محددين</strong>: توزيع عادل بين قائمة موظفين تحددها من صفحة الإعدادات (اختيار بمربعات تحديد).</li>
                                        <li><strong>تلقائي لكل الموظفين</strong>: نفس التوزيع العادل، لكن بين كل مستخدمي الدور "agent" المتاحين للتعيين (بلا حاجة لتحديد قائمة).</li>
                                    </ul>
                                    <p class="text-sm mt-2">"العدالة" هنا تعني: تذهب كل محادثة جديدة لمن لديه حالياً <strong>أقل عدد محادثات مفتوحة</strong> من المجموعة المؤهّلة — توازن حمل حقيقي، وليس تناوباً دورياً أعمى لا يراعي تراكم محادثات موظف معيّن (إجازة/بطء). التعيين يحدث فقط للمحادثات الجديدة فعلياً — لا يُعاد توزيع محادثة مستمرة، فيبقى نفس الموظف يتابع نفس العميل. قواعد الأتمتة (تعيين حسب رقم/كلمة مفتاحية) تبقى تعمل بعد هذا التوزيع ويمكنها إلغاءه لحالات محددة.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-teal-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">2. إتاحة/استثناء موظف من التوزيع التلقائي</h3>
                                    <p class="text-sm">من صفحة "المستخدمين"، عمود "التوزيع التلقائي" لكل موظف — تبديل سريع بضغطة واحدة لاستثناء موظف مؤقتاً (إجازة/انشغال) من دورة التوزيع بلا حذفه من القائمة المحددة في الإعدادات أو تعديل دوره.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-pink-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">3. تنبيه الموظف عند تعيين محادثة له</h3>
                                    <p class="text-sm mb-2">عند تعيين محادثة لموظف (تلقائياً أو يدوياً)، يصله تنبيهان (يتضمنان اسم/رقم العميل وآخر رسالة منه):</p>
                                    <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                        <li><strong>جرس الإشعارات</strong> في الشريط العلوي — عدّاد للإشعارات غير المقروءة، بقائمة منسدلة، مع استطلاع دوري كل 20 ثانية (لا يحتاج تحديث الصفحة). يمكن أيضاً تفعيل تنبيهات المتصفح (Desktop Notification) من نفس القائمة.</li>
                                        <li><strong>رسالة واتساب فعلية</strong> — تصل فقط إن كان للموظف رقم واتساب مسجَّل (حقل اختياري جديد في صفحة تعديل المستخدم). بلا رقم، يبقى إشعار الجرس وحده كافياً.</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">15. الصيانة الذاتية للنظام (تذكيرات، تنبيهات صحة، نسخ احتياطي)</h2>
                            <p class="mb-4">مجموعة أوامر مجدولة تلقائياً (راجع <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">routes/console.php</code>) هدفها تقليل الحاجة للمراقبة اليدوية اليومية للنظام.</p>

                            <div class="space-y-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-amber-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">تذكير تلقائي بطلبات الموافقة المعلّقة</h3>
                                    <p class="text-sm">الأمر <code class="bg-white dark:bg-gray-800 px-1">printing:send-approval-reminders</code> (كل 10 دقائق) يُرسل تذكير واتساب لأي طلب موافقة (طباعة أو إرسال) لا يزال معلّقاً بعد <code class="bg-white dark:bg-gray-800 px-1">PRINTING_APPROVAL_REMINDER_AFTER_MINUTES</code> دقيقة (افتراضي 20)، ثم يكرره كل <code class="bg-white dark:bg-gray-800 px-1">PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES</code> دقيقة (افتراضي 30) ما دام معلّقاً. اضبط القيمة الثانية على 0 لتعطيل التذكير كلياً.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-red-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">تنبيه واتساب عند تعطل معالجة الطابور</h3>
                                    <p class="text-sm">أمر <code class="bg-white dark:bg-gray-800 px-1">monitor:system</code> الموجود أصلاً (كل 10 دقائق) يرسل الآن أيضاً تنبيه واتساب فوري لرقم <code class="bg-white dark:bg-gray-800 px-1">PRINTER_ALERT_PHONE</code> عند تراكم كبير في طابور المعالجة (فوق <code class="bg-white dark:bg-gray-800 px-1">HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD</code>، افتراضي 50) أو رسائل معلّقة منذ أكثر من 10 دقائق — مؤشر شائع لتوقف Queue Worker بصمت. تنبيه واحد فقط ثم فترة تهدئة (<code class="bg-white dark:bg-gray-800 px-1">HEALTH_ALERT_COOLDOWN_MINUTES</code>، افتراضي 60 دقيقة) قبل تكراره، مع إشعار تعافٍ منفصل بمجرد زوال المشكلة.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-emerald-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">نسخ احتياطي يومي تلقائي لقاعدة البيانات</h3>
                                    <p class="text-sm">الأمر <code class="bg-white dark:bg-gray-800 px-1">backup:database</code> (يومياً الساعة 3 فجراً) ينشئ نسخة mysqldump مضغوطة (gzip) في <code class="bg-white dark:bg-gray-800 px-1">storage/app/backups</code>، ويحذف تلقائياً أي نسخة أقدم من <code class="bg-white dark:bg-gray-800 px-1">BACKUP_RETENTION_DAYS</code> يوماً (افتراضي 14). يتطلب ضبط <code class="bg-white dark:bg-gray-800 px-1">MYSQLDUMP_PATH</code> في <code class="bg-white dark:bg-gray-800 px-1">.env</code> (الافتراضي يطابق مسار XAMPP القياسي). يرسل تنبيه واتساب لصاحب المنشأة عند فشل النسخ الاحتياطي.</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border-r-4 border-cyan-500">
                                    <h3 class="text-lg font-bold mb-2 text-[#f53003]">تتبع عدد الصفحات المطبوعة</h3>
                                    <p class="text-sm">كل مهمة طباعة تُحتسَب صفحاتها فعلياً (من ملف PDF النهائي، أو صفحة واحدة لكل صورة) وتُجمَّع في عمود "الصفحات المطبوعة" لكل طابعة (صفحة "الطابعات") — تقدير تقريبي مفيد لتخطيط استهلاك الحبر/الورق، وليس عداداً رسمياً دقيقاً.</p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-10">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">16. تعديل وحذف رسائل واتساب (Message Edited / Deleted)</h2>
                            <p class="mb-4">عندما يعدّل أو يحذف عميل رسالة سبق إرسالها عبر واتساب (ميزتا واتساب المعروفتان)، ينعكس ذلك تلقائياً في محادثته بالنظام المحلي، بدل أن تبقى النسخة القديمة معروضة كأن شيئاً لم يحدث.</p>
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><strong>تعديل:</strong> يُستبدَل نص الرسالة المعروض بالنص الجديد، مع الاحتفاظ بالنص السابق (وأي تعديلات سابقة) في سجل داخلي للتدقيق عند الحاجة.</li>
                                    <li><strong>حذف:</strong> يُستبدَل المحتوى بعبارة "🚫 تم حذف هذه الرسالة" (نفس اتفاقية واتساب نفسها) بدل حذف السجل فعلياً — يبقى النص/الملف الأصليان محفوظين داخلياً.</li>
                                </ul>
                                <p class="text-xs text-gray-500 mt-2">تتطلب هذه الميزة أن يكون النظام المركزي قد فعّل هذين الحدثين ضمن إعدادات الويب هوك الخاصة بشركتك (صفحة "الأنظمة الخارجية المرتبطة" في النظام المركزي) — راجع مطوّر النظام المركزي إن لم تلاحظ هذا السلوك فعلياً رغم تعديل/حذف رسالة حقيقية.</p>
                            </div>
                        </section>

                        <section id="webhook-token-separation" class="mb-12">
                            <h2 class="text-2xl font-semibold mb-4 border-b pb-2">17. فصل توكن التحقق من الويب هوك عن توكن الاتصال الصادر (CENTRAL_WEBHOOK_TOKEN)</h2>
                            <p class="mb-4">سابقاً كان النظام المحلي يستخدم توكناً واحداً (<code>CENTRAL_API_TOKEN</code>) لغرضين مختلفين تماماً: الاتصال الصادر بالنظام المركزي، والتحقق من أن طلبات الويب هوك الواردة فعلاً من النظام المركزي. توحيد الاتجاهين في سرّ واحد يخالف أفضل ممارسات الأمان — فصل بيانات الاعتماد حسب حدود الثقة — لأن أي تسريب لأحد الاتجاهين (سجلات، دعم فني، إلخ) يكشف الطرفين معاً، ولا يمكن تدوير أحدهما بمعزل عن الآخر.</p>
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg mb-4">
                                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                                    <li><strong>CENTRAL_API_TOKEN (صادر):</strong> يُرسله هذا النظام كـ Bearer عند استدعاء واجهات برمجة النظام المركزي (رفع الرسائل، تحديث الحالة، إلخ). يبقى كما هو دون تغيير.</li>
                                    <li><strong>CENTRAL_WEBHOOK_TOKEN (وارد، جديد):</strong> توكن مستقل يتحقق به النظام المحلي من أن أي طلب واصل على مسارات <code>/api/webhook/*</code> فعلاً من النظام المركزي، وليس من طرف ثالث.</li>
                                </ul>
                            </div>
                            <p class="mb-2 font-medium">خطوات التفعيل (اختياري، متوافق خلفياً بالكامل):</p>
                            <ol class="list-decimal list-inside text-sm space-y-1 mr-4 mb-4">
                                <li>من النظام المركزي، افتح صفحة "الأنظمة الخارجية المرتبطة" الخاصة بشركتك وحدّد نقطة الويب هوك المرتبطة بهذا النظام المحلي (وليس التوكن السري العام للشركة)، ثم انسخ قيمة "التوكن" الخاصة بها — يدعم النظام المركزي أصلاً توكناً مستقلاً لكل نقطة ويب هوك.</li>
                                <li>من هذا النظام، افتح <a href="{{ route('settings.index') }}" class="text-indigo-600 hover:underline">الإعدادات</a> ثم بطاقة "الواجهة المركزية (Central API)"، والصق القيمة في حقل "رمز التحقق من الويب هوك الوارد (CENTRAL_WEBHOOK_TOKEN)".</li>
                                <li>احفظ الإعدادات. من هذه اللحظة يتحقق النظام المحلي من طلبات الويب هوك الواردة بهذا التوكن المستقل بدل الرجوع إلى CENTRAL_API_TOKEN.</li>
                            </ol>
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg mb-4">
                                <p class="text-xs text-gray-500">إن تُرك الحقل فارغاً، يستمر النظام في قبول التوكن القديم (CENTRAL_API_TOKEN) للتحقق من الويب هوك كما كان الحال دائماً — لا حاجة لأي إجراء فوري، ولن ينكسر أي تكامل قائم. الفصل موصى به فقط كخطوة تحصين إضافية، وليس إلزامياً للاستمرار في العمل.</p>
                            </div>
                            <p class="mb-2 font-medium">تحقق إضافي من التوقيع (X-Webhook-Signature):</p>
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg">
                                <p class="text-sm mb-2">إلى جانب التوكن، يوقّع النظام المركزي كل طلب ويب هوك بتوقيع HMAC-SHA256 (ترويسة <code>X-Webhook-Signature</code>) محسوباً من نفس التوكن أعلاه — يثبت أن جسم الطلب لم يُعدَّل أثناء النقل، وليس فقط أن المُرسِل يملك التوكن الصحيح. يتحقق النظام المحلي من هذا التوقيع تلقائياً كلما وُجدت الترويسة، ويرفض الطلب (401) إن لم يتطابق. الطلبات القديمة أو من أنظمة خارجية لا تُرسل هذه الترويسة تستمر بالعمل بالتوكن وحده (توافق خلفي).</p>
                                <p class="text-xs text-gray-500">تنبيه فني: كان التوقيع سابقاً يُحسب بترميز JSON مختلف عن الترميز الفعلي المُرسَل في جسم الطلب، ما كان يجعل أي تحقق للتوقيع يفشل تلقائياً مع أي محتوى عربي. تم إصلاح هذا في النظام المركزي بحيث يُرسَل جسم الطلب بنفس السلسلة الحرفية الموقَّعة تماماً.</p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
