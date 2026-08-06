<?php

return [

    // مفتاح رئيسي لتفعيل/تعطيل ميزة الطباعة الذكية بالكامل
    'enabled' => (bool) env('PRINTING_ENABLED', false),

    // مسار SumatraPDF.exe (أداة الطباعة الصامتة لملفات PDF) — يجب تنزيلها يدوياً من
    // https://www.sumatrapdfreader.org ووضعها في هذا المسار (النسخة المحمولة/portable كافية)
    'sumatra_path' => env('SUMATRA_PDF_PATH', 'C:/SumatraPDF/SumatraPDF.exe'),

    // مهلة تنفيذ أمر الطباعة بالثواني — لوحظ عملياً أن ملفات PDF كبيرة/تحتوي صوراً قد تستغرق
    // وقتاً أطول من المتوقع تحت مهمة Task Scheduler التفاعلية (زمن إطلاق إضافي)، لذا تُرِك هامش
    // أمان أكبر من القيمة الافتراضية القديمة (60) التي تسببت بفشل زائف رغم نجاح الطباعة فعلياً.
    'print_timeout' => (int) env('PRINT_TIMEOUT_SECONDS', 180),

    // مسار تخزين نسخ محلية من الملفات الواردة المطلوب طباعتها (على قرص 'local' الخاص)
    'download_path' => 'print_jobs',

    // امتدادات الملفات القابلة للطباعة الآلية — PDF والصور تُرسل مباشرة لـ SumatraPDF (يدعمها بلا أي
    // تحويل). ملفات Word/Excel/PowerPoint (doc/docx/xls/xlsx/ppt/pptx) تُحوَّل أولاً إلى PDF تلقائياً
    // عبر LibreOffice (راجع libreoffice_path أدناه) ثم تُطبع بنفس مسار PDF المعتاد.
    'printable_extensions' => array_map('trim', explode(',', env(
        'PRINTABLE_EXTENSIONS',
        'pdf,jpg,jpeg,png,gif,bmp,tif,tiff,doc,docx,xls,xlsx,ppt,pptx'
    ))),

    // امتدادات ملفات الأوفيس التي تحتاج تحويلاً لـ PDF عبر LibreOffice قبل الطباعة — لا تُضاف هنا
    // إلا صيغة يدعم LibreOffice --headless --convert-to pdf تحويلها فعلياً.
    'office_extensions' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],

    // مسار soffice.exe (LibreOffice) المستخدم لتحويل ملفات الأوفيس إلى PDF بصمت (headless) قبل
    // الطباعة. يجب تثبيت LibreOffice ووضع مساره هنا (النسخة الكاملة، وليست المحمولة).
    'libreoffice_path' => env('LIBREOFFICE_PATH', 'C:/Program Files/LibreOffice/program/soffice.exe'),

    // مهلة تحويل ملف أوفيس إلى PDF بالثواني — يُلاحَظ عملياً أن أول تشغيل لـ LibreOffice بعد فترة
    // خمول يستغرق وقتاً إضافياً لتهيئة ملفه الشخصي (profile)، لذا هامش أكبر من مهلة الطباعة نفسها.
    'office_conversion_timeout' => (int) env('OFFICE_CONVERSION_TIMEOUT_SECONDS', 120),

    // رقم واتساب يُرسَل له تنبيه فوري عند تغيّر حالة أي طابعة (توقف/نفاد ورق أو حبر/انحشار...)
    // اتركه فارغاً لتعطيل التنبيهات (سيبقى السجل مرئياً في لوحة التحكم رغم ذلك)
    'alert_phone_number' => env('PRINTER_ALERT_PHONE'),

    // هل يُرسَل رد تلقائي لمن طلب الطباعة (عبر واتساب) بحالة طلبه — نجحت الطباعة أم فشلت ولماذا؟
    'reply_status_to_sender' => (bool) env('PRINTING_REPLY_STATUS_TO_SENDER', true),

    // هل يُرسَل رد فوري "تم استلام طلبك" لحظة تسجيل طلب الطباعة (قبل تنفيذه فعلياً)، منفصل عن رد
    // النتيجة النهائية أعلاه — يطمئن العميل أن ملفه وصل بشكل صحيح دون انتظار نتيجة الطباعة نفسها.
    'reply_ack_on_receipt' => (bool) env('PRINTING_REPLY_ACK_ON_RECEIPT', true),

    // هل يُرسَل تنبيه فني مفصّل (بالخطأ الحقيقي غير المبسَّط) لصاحب المنشأة عبر alert_phone_number
    // عند فشل طلب طباعة نهائياً (بعد استنفاد كل المحاولات)؟ منفصل عن تنبيهات صحة الطابعة أعلاه.
    'notify_owner_on_job_failure' => (bool) env('PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE', true),

    // [Fix 2026-08-06] صور واتساب تصل بلا بيانات دقة (DPI)، فيحسب SumatraPDF حجم صفحة الطباعة
    // مباشرة من أبعاد الصورة بالبكسل، منتجاً حجم صفحة غير قياسي (custom) لا يطابق أي درج ورق —
    // يظهر خطأ "الورق غير موجود" رغم وجود ورق A4/Letter فعلياً. الحل: نضع الصورة داخل صفحة قياسية
    // كاملة (بخلفية بيضاء) بهذا المقاس قبل الطباعة، بدل ترك SumatraPDF يشتق مقاس الصفحة من الصورة.
    // القيمة يجب أن تطابق مقاس الورق المُحمَّل فعلياً في الطابعة.
    'image_page_size' => env('PRINT_IMAGE_PAGE_SIZE', 'a4'), // a4 أو letter
    'image_dpi' => (int) env('PRINT_IMAGE_DPI', 200),
];
