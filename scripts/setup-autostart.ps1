<#
    إعداد التشغيل التلقائي الكامل للنظام المحلي عند إقلاع Windows:
    - نسخ Apache من تثبيت XAMPP إلى نسخة معزولة خاصة بهذا المشروع فقط (apache-standalone)،
      بمنفذ وإعدادات خاصة به، بدون أي تعديل على إعدادات XAMPP المشتركة أو تأثير على أي مشروع
      آخر مثبّت على نفس الجهاز (يتفادى تعارضات المنافذ مع Apache أخرى/IIS/برامج أخرى قد تكون
      موجودة مسبقاً لدى العميل على المنفذ 80/443).
    - تسجيل هذه النسخة المعزولة كخدمة Windows تعمل تلقائياً عند الإقلاع.
    - [Fix 2026-08-06] تسجيل MySQL نفسه (نفس تثبيت/بيانات XAMPP الحالية، بلا نسخ أو تعديل) كخدمة
      Windows تلقائية أيضاً — لوحظ فعلياً أن XAMPP لا يُسجِّل MySQL كخدمة افتراضياً (يحتاج تشغيلاً
      يدوياً من لوحة تحكم XAMPP بعد كل إقلاع)، وبدونه يفشل الموقع بالكامل (HTTP 500) لأن كل صفحة
      تحتاج قاعدة البيانات، حتى مع نجاح Apache نفسه.
    - تسجيل عامل الطابور (queue:work) والمجدول (schedule:work) كمهام مجدولة تعمل عند الإقلاع
      **وعند تسجيل الدخول** (الأخيرة أكثر موثوقية للمهام التفاعلية)، وتُعيد تشغيل نفسها تلقائياً
      عند التعطل، بدل نوافذ CMD يدوية يجب فتحها كل يوم.

    الاستخدام: شغّل 03-Install-AutoStart.bat (سيطلب صلاحيات المسؤول تلقائياً)، أو نفّذ هذا الملف
    مباشرة من PowerShell كمسؤول:  .\setup-autostart.ps1
    للإزالة: .\setup-autostart.ps1 -Uninstall
#>

param(
    [string]$XamppPath = "C:\xampp",
    [string]$ProjectPath = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path,
    [switch]$Uninstall
)

$ErrorActionPreference = "Stop"

$phpExe = Join-Path $XamppPath "php\php.exe"

$standaloneApachePath = Join-Path $PSScriptRoot "apache-standalone"
$standaloneHttpdExe   = Join-Path $standaloneApachePath "bin\httpd.exe"
$standaloneHttpdConf  = Join-Path $standaloneApachePath "conf\httpd.conf"

$apacheServiceName = "WhatsAppLocalApache"
$mysqlServiceName  = "MySQL_XAMPP"
$queueTaskName      = "WhatsAppLocalSystem-QueueWorker"
$schedulerTaskName  = "WhatsAppLocalSystem-Scheduler"

$mysqldExe = Join-Path $XamppPath "mysql\bin\mysqld.exe"
$mysqlIni  = Join-Path $XamppPath "mysql\bin\my.ini"

function Write-Step($msg) { Write-Host "==> $msg" -ForegroundColor Cyan }

# تُنهي أي عملية php.exe يتيمة تطابق أمر artisan معيّن، بقيت خارج تتبع Task Scheduler
# (لوحظ أن Stop-ScheduledTask لا يضمن دائماً إنهاء العملية الفعلية على Windows، مما قد يسبب
# تشغيل عاملين متوازيين لنفس المهمة عند إعادة تشغيل هذا السكربت، أو بقاءها بعد الإزالة)
function Stop-OrphanWorkerProcess {
    param([string]$ArtisanCommand)

    $expectedCommandLine = "`"$phpExe`" artisan $ArtisanCommand"
    Get-CimInstance Win32_Process -Filter "Name='php.exe'" -ErrorAction SilentlyContinue |
        Where-Object { $_.CommandLine -eq $expectedCommandLine } |
        ForEach-Object {
            Write-Host "   إنهاء عملية يتيمة سابقة (PID $($_.ProcessId))..." -ForegroundColor DarkYellow
            Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue
        }
}

if (-not ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    throw "يجب تشغيل هذا السكربت بصلاحيات المسؤول (Administrator). استخدم 03-Install-AutoStart.bat بدلاً من ذلك."
}

if ($Uninstall) {
    Write-Step "إزالة الإعداد التلقائي..."

    foreach ($t in @($queueTaskName, $schedulerTaskName)) {
        if (Get-ScheduledTask -TaskName $t -ErrorAction SilentlyContinue) {
            Stop-ScheduledTask -TaskName $t -ErrorAction SilentlyContinue
        }
    }
    Start-Sleep -Seconds 1
    Stop-OrphanWorkerProcess -ArtisanCommand "queue:work"
    Stop-OrphanWorkerProcess -ArtisanCommand "schedule:work"

    Unregister-ScheduledTask -TaskName $queueTaskName -Confirm:$false -ErrorAction SilentlyContinue
    Unregister-ScheduledTask -TaskName $schedulerTaskName -Confirm:$false -ErrorAction SilentlyContinue

    if (Get-Service -Name $apacheServiceName -ErrorAction SilentlyContinue) {
        Stop-Service -Name $apacheServiceName -Force -ErrorAction SilentlyContinue
        if (Test-Path $standaloneHttpdExe) {
            & $standaloneHttpdExe -k uninstall -n $apacheServiceName
        }
    }

    # ملاحظة: لا نزيل خدمة MySQL هنا عمداً — قد تُستخدم قاعدة بياناتها من مشاريع أخرى على نفس
    # الجهاز، وإزالتها مفاجئة قد تقطع خدمة عن غير قصد. إن أردت إزالتها فعلاً، استخدم لوحة تحكم
    # الخدمات (services.msc) يدوياً بعد التأكد أن لا مشروع آخر يعتمد عليها.

    Write-Host "تمت إزالة كل مهام/خدمات التشغيل التلقائي. (النسخة المعزولة من Apache في $standaloneApachePath وخدمة MySQL ($mysqlServiceName) لم تُحذفا، يمكن حذفهما يدوياً إن أردت)." -ForegroundColor Green
    exit 0
}

$sourceApachePath = Join-Path $XamppPath "apache"

if (!(Test-Path $phpExe))         { throw "لم يتم العثور على php.exe في: $phpExe — مرّر -XamppPath الصحيح إذا كان XAMPP مثبّتاً في مسار مختلف." }
if (!(Test-Path $sourceApachePath)) { throw "لم يتم العثور على مجلد Apache في: $sourceApachePath" }
if (!(Test-Path (Join-Path $ProjectPath "artisan"))) { throw "لم يتم العثور على مجلد المشروع (artisan) في: $ProjectPath" }

# استخراج المنفذ من APP_URL في ملف .env الخاص بالمشروع (افتراضياً 8006 إن لم يوجد)
$appPort = 8006
$envFile = Join-Path $ProjectPath ".env"
if (Test-Path $envFile) {
    $appUrlLine = Get-Content $envFile | Where-Object { $_ -match '^\s*APP_URL\s*=' } | Select-Object -First 1
    if ($appUrlLine -and ($appUrlLine -match ':(\d+)\s*$')) {
        $appPort = [int]$Matches[1]
    }
}

Write-Host "مسار XAMPP (للنسخ منه فقط، لن يُعدَّل): $XamppPath"
Write-Host "مسار المشروع: $ProjectPath"
Write-Host "منفذ الخدمة: $appPort"
Write-Host ""

# 1) إنشاء/تحديث نسخة Apache معزولة خاصة بهذا المشروع فقط (لا تلمس تثبيت XAMPP الأصلي إطلاقاً)
Write-Step "تجهيز نسخة Apache معزولة في: $standaloneApachePath ..."

if (!(Test-Path $standaloneApachePath)) {
    Write-Host "   نسخ ملفات Apache من XAMPP (مرة واحدة فقط، قد يستغرق دقيقة)..."
    robocopy $sourceApachePath $standaloneApachePath /MIR /XD logs /NFL /NDL /NJH /NJS | Out-Null
    New-Item -ItemType Directory -Path (Join-Path $standaloneApachePath "logs") -Force | Out-Null
}

if (!(Test-Path $standaloneHttpdExe)) { throw "فشل نسخ Apache: لم يتم العثور على $standaloneHttpdExe" }

# تعديل إعدادات النسخة المعزولة فقط: ServerRoot الخاص بها، منفذها، مجلد المشروع كجذر للمستندات، وتعطيل SSL
$standaloneApachePathForward = $standaloneApachePath -replace '\\', '/'
$projectPublicPath = (Join-Path $ProjectPath "public") -replace '\\', '/'

$conf = Get-Content $standaloneHttpdConf -Raw
$conf = $conf -replace 'ServerRoot\s+"[^"]*"', "ServerRoot `"$standaloneApachePathForward`""
$conf = $conf -replace '(?m)^Listen\s+\d+\s*$', "Listen $appPort"
$conf = $conf -replace 'DocumentRoot\s+"[^"]*"', "DocumentRoot `"$projectPublicPath`""
$conf = $conf -replace '<Directory\s+"[^"]*xampp[/\\]htdocs">', "<Directory `"$projectPublicPath`">"
$conf = $conf -replace '(?m)^Include conf/extra/httpd-ssl\.conf\s*$', '#Include conf/extra/httpd-ssl.conf'
Set-Content -Path $standaloneHttpdConf -Value $conf -Encoding ASCII

& $standaloneHttpdExe -t
if ($LASTEXITCODE -ne 0) { throw "فشل التحقق من إعدادات Apache المعزولة. راجع الرسائل أعلاه." }

Write-Host "   إعدادات النسخة المعزولة صحيحة (منفذ $appPort، جذر المستندات: $projectPublicPath)." -ForegroundColor Green

# 2) تسجيل النسخة المعزولة كخدمة Windows تعمل تلقائياً عند الإقلاع
Write-Step "تسجيل خدمة Windows للنسخة المعزولة..."
if (Get-Service -Name $apacheServiceName -ErrorAction SilentlyContinue) {
    Stop-Service -Name $apacheServiceName -Force -ErrorAction SilentlyContinue
    & $standaloneHttpdExe -k uninstall -n $apacheServiceName
}
& $standaloneHttpdExe -k install -n $apacheServiceName
Set-Service -Name $apacheServiceName -StartupType Automatic
Start-Service -Name $apacheServiceName
Write-Host "   Apache (نسخة معزولة) مسجّل كخدمة ($apacheServiceName) على المنفذ $appPort، وسيعمل تلقائياً مع كل إقلاع." -ForegroundColor Green

# 3) تسجيل MySQL (نفس تثبيت/بيانات XAMPP الحالية بلا أي تغيير) كخدمة Windows تلقائية
#
# [Fix 2026-08-06] لوحظ فعلياً: XAMPP لا يُسجِّل MySQL كخدمة Windows افتراضياً — يبقى يحتاج تشغيلاً
# يدوياً من لوحة تحكم XAMPP بعد كل إقلاع، وبدونه يفشل الموقع بالكامل (HTTP 500، "connection
# refused") رغم نجاح Apache وكل شيء آخر، لأن كل صفحة تحتاج قاعدة البيانات. هذا يُسجِّل نفس تثبيت
# MySQL الموجود (بنفس مجلد البيانات) كخدمة — لا يُنشئ نسخة منفصلة ولا يُغيّر أي بيانات.
Write-Step "تسجيل خدمة Windows لـ MySQL..."
if (!(Test-Path $mysqldExe)) {
    Write-Host "   تحذير: لم يتم العثور على mysqld.exe في $mysqldExe — تخطّي تسجيل خدمة MySQL. تأكد من تشغيله يدوياً من لوحة تحكم XAMPP." -ForegroundColor Yellow
} else {
    if (Get-Service -Name $mysqlServiceName -ErrorAction SilentlyContinue) {
        Stop-Service -Name $mysqlServiceName -Force -ErrorAction SilentlyContinue
        & $mysqldExe --remove $mysqlServiceName | Out-Null
    }
    & $mysqldExe --install $mysqlServiceName --defaults-file="$mysqlIni" | Out-Null
    Set-Service -Name $mysqlServiceName -StartupType Automatic
    Start-Service -Name $mysqlServiceName
    Write-Host "   MySQL مسجّل كخدمة ($mysqlServiceName)، وسيعمل تلقائياً مع كل إقلاع." -ForegroundColor Green
}

# دالة مساعدة: تسجيل مهمة مجدولة تُشغّل أمر php artisan دائم وتعيد تشغيل نفسها عند التعطل
#
# [Fix 2026-08-04] عامل الطابور (queue:work) تحديداً يحتاج تشغيلاً تفاعلياً (Interactive) لا
# SYSTEM: تحققنا عملياً أن طباعة PDF حقيقي (يحتوي صوراً) عبر SumatraPDF تُعلَّق حتى انتهاء المهلة
# عند التشغيل بحساب SYSTEM (بيئة Session 0 المعزولة بلا سطح مكتب فعلي تحتاجه عمليات GDI للطباعة)،
# بينما تنجح فوراً (~24 ثانية) لنفس الملف بالضبط عند التشغيل بحساب مستخدم تفاعلي حقيقي — هذا قيد
# معروف وموثّق لخدمات Windows التي تطبع عبر GDI. لا يحتاج كلمة مرور مخزّنة: LogonType Interactive
# بدون Password يجعل المهمة تعمل فقط عندما يكون هذا المستخدم مسجّلاً دخوله فعلياً على الجهاز
# (بديل مقبول تماماً لجهاز مكتب واحد يبقى مسجّلاً دخوله باستمرار).
function Register-WorkerTask {
    param([string]$TaskName, [string]$ArtisanCommand, [switch]$RequiresInteractiveSession)

    if (Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue) {
        Stop-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
        Start-Sleep -Seconds 1
    }
    Stop-OrphanWorkerProcess -ArtisanCommand $ArtisanCommand

    $action = New-ScheduledTaskAction -Execute $phpExe -Argument "artisan $ArtisanCommand" -WorkingDirectory $ProjectPath

    # [Fix 2026-08-06] لوحظ فعلياً: محفّز "عند الإقلاع" (AtStartup) وحده لمهمة تحتاج جلسة تفاعلية
    # (Interactive) قد يفشل بصمت إذا نُفِّذ قبل اكتمال تسجيل دخول المستخدم المحدد فعلياً على سطح
    # المكتب (Task Scheduler لا يعيد المحاولة تلقائياً في هذه الحالة رغم RestartCount أدناه، لأن
    # المهمة لم "تبدأ" أصلاً من منظوره ليُعاد تشغيلها). محفّز "عند تسجيل الدخول" (AtLogOn) لنفس
    # المستخدم أكثر موثوقية للمهام التفاعلية لأنه يُطلق فعلياً بعد اكتمال تسجيل الدخول، ونُبقي
    # محفّز الإقلاع أيضاً كطبقة أمان إضافية (يفيد المهام غير التفاعلية أو حال نجاحه أحياناً).
    $triggerStartup = New-ScheduledTaskTrigger -AtStartup
    $triggerDaily    = New-ScheduledTaskTrigger -Daily -At 4am
    $triggers = @($triggerStartup, $triggerDaily)

    if ($RequiresInteractiveSession) {
        $currentUser = (Get-CimInstance Win32_ComputerSystem).UserName
        if (!$currentUser) {
            Write-Host "   تحذير: لا يوجد مستخدم مسجّل دخوله حالياً على الجهاز، سيُسجَّل عامل الطابور بحساب SYSTEM مؤقتاً (قد تفشل طباعة ملفات معقدة حتى تُعاد تشغيل السكربت أثناء تسجيل دخول فعلي)." -ForegroundColor Yellow
            $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
        } else {
            $principal = New-ScheduledTaskPrincipal -UserId $currentUser -LogonType Interactive -RunLevel Highest
            $triggers += New-ScheduledTaskTrigger -AtLogOn -User $currentUser
        }
    } else {
        $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
    }

    $settings = New-ScheduledTaskSettingsSet `
        -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable `
        -RestartCount 999 -RestartInterval (New-TimeSpan -Minutes 1) `
        -ExecutionTimeLimit (New-TimeSpan -Days 0) `
        -MultipleInstances IgnoreNew

    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false -ErrorAction SilentlyContinue
    Register-ScheduledTask -TaskName $TaskName -Action $action -Trigger $triggers `
        -Principal $principal -Settings $settings | Out-Null

    Start-ScheduledTask -TaskName $TaskName
}

Write-Step "تسجيل مهمة تشغيل عامل الطابور (queue:work)..."
Register-WorkerTask -TaskName $queueTaskName -ArtisanCommand "queue:work" -RequiresInteractiveSession
Write-Host "   تم تسجيل وتشغيل المهمة: $queueTaskName" -ForegroundColor Green

Write-Step "تسجيل مهمة تشغيل المجدول (schedule:work)..."
Register-WorkerTask -TaskName $schedulerTaskName -ArtisanCommand "schedule:work"
Write-Host "   تم تسجيل وتشغيل المهمة: $schedulerTaskName" -ForegroundColor Green

Write-Host ""
Write-Host "تم الإعداد بنجاح! سيعمل النظام بالكامل تلقائياً بعد كل إعادة تشغيل للجهاز دون أي تدخل يدوي." -ForegroundColor Green
Write-Host "الرابط: http://localhost:$appPort" -ForegroundColor Green
Write-Host ""
Write-Host "ملاحظة مهمة: بعد هذا الإعداد، لا تستخدم أزرار (تشغيل/إيقاف/إعادة تشغيل الخدمات) في" -ForegroundColor Yellow
Write-Host "لوحة تحكم النظام، لأن العمليات أصبحت مُدارة بواسطة Windows Task Scheduler مباشرة —" -ForegroundColor Yellow
Write-Host "استخدامها معاً قد يؤدي لتشغيل عاملين مكررين لنفس المهمة." -ForegroundColor Yellow
Write-Host ""
Write-Host "للإزالة لاحقاً: نفّذ 04-Uninstall-AutoStart.bat" -ForegroundColor DarkGray



