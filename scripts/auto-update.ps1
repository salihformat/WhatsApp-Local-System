<#
    سحب تلقائي للتحديثات من فرع main على GitHub، وتطبيقها بالكامل بلا تدخل يدوي:
    فحص وجود تحديث جديد -> git pull -> composer/npm -> migrate -> تنظيف الكاش -> إعادة تشغيل
    عامل الطابور (ضروري دائماً بعد أي تعديل كود — راجع صفحة /docs، القسم 8).

    مُصمَّم ليُشغَّل دورياً عبر مهمة مجدولة (يسجّلها setup-autostart.ps1)، وآمن للتشغيل المتكرر:
    لا يفعل شيئاً إن لم يوجد تحديث جديد، ولا يلمس أي تغييرات محلية غير مرفوعة (يتوقف ويُسجّل
    تحذيراً بدل تجاهلها أو حذفها، حمايةً من فقدان عمل غير متعمّد).
#>

param(
    [string]$ProjectPath = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path,
    [string]$Branch = "main"
)

$ErrorActionPreference = "Stop"
$logFile = Join-Path $ProjectPath "storage\logs\auto-update.log"

function Write-Log($msg) {
    $line = "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] $msg"
    Add-Content -Path $logFile -Value $line -Encoding UTF8
}

Set-Location $ProjectPath

try {
    # لا نلمس شجرة عمل غير نظيفة (تعديلات محلية غير مرفوعة) — قد تكون تجربة يدوية مقصودة على
    # جهاز الإنتاج، والسحب/الدمج فوقها تلقائياً قد يُفقدها أو يُنتج تعارضاً غير متوقَّع.
    $dirty = git status --porcelain
    if ($dirty) {
        Write-Log "تخطّي: توجد تعديلات محلية غير مرفوعة (working tree غير نظيفة). راجعها يدوياً أولاً."
        exit 0
    }

    git fetch origin $Branch --quiet 2>&1 | Out-Null

    $localCommit = git rev-parse HEAD
    $remoteCommit = git rev-parse "origin/$Branch"

    if ($localCommit -eq $remoteCommit) {
        # لا يوجد تحديث جديد — الحالة الطبيعية لمعظم التشغيلات، بلا تسجيل لتفادي إغراق السجل
        exit 0
    }

    Write-Log "تحديث جديد متوفر: $localCommit -> $remoteCommit — جاري التطبيق..."

    git pull origin $Branch --ff-only 2>&1 | ForEach-Object { Write-Log "  git: $_" }

    $phpExe = "C:\xampp\php\php.exe"

    Write-Log "تثبيت حزم PHP (composer install)..."
    composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | ForEach-Object { Write-Log "  composer: $_" }

    Write-Log "تثبيت وبناء حزم الواجهة (npm)..."
    npm install 2>&1 | ForEach-Object { Write-Log "  npm: $_" }
    npm run build 2>&1 | ForEach-Object { Write-Log "  npm build: $_" }

    # [Fix 2026-08-06] نسخة احتياطية سريعة لقاعدة البيانات قبل migrate مباشرة — لو وصل تحديث فيه
    # migration به خطأ (يحذف عمود/جدولاً مثلاً)، هذه النسخة هي طريقة التراجع الوحيدة المتاحة بما أن
    # كل هذا يعمل تلقائياً بلا مراجعة بشرية. إن فشل النسخ الاحتياطي نفسه، نتوقف قبل migrate عمداً
    # (أفضل تأجيل تحديث آمن من تطبيق migration خطر بلا أي نسخة نرجع إليها).
    $envContent = Get-Content (Join-Path $ProjectPath ".env") -Raw
    $dbName = if ($envContent -match '(?m)^\s*DB_DATABASE\s*=\s*(\S+)\s*$') { $Matches[1] } else { $null }
    $dbUser = if ($envContent -match '(?m)^\s*DB_USERNAME\s*=\s*(\S*)\s*$') { $Matches[1] } else { 'root' }
    $dbPass = if ($envContent -match '(?m)^\s*DB_PASSWORD\s*=\s*(\S*)\s*$') { $Matches[1] } else { '' }

    if ($dbName) {
        $backupDir = Join-Path $ProjectPath "storage\app\private\db_backups"
        New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
        $backupFile = Join-Path $backupDir "backup_$(Get-Date -Format 'yyyy-MM-dd_HHmmss').sql"

        Write-Log "نسخ احتياطي لقاعدة البيانات قبل migrate..."
        $mysqldumpArgs = @('-u', $dbUser)
        if ($dbPass) { $mysqldumpArgs += "-p$dbPass" }
        $mysqldumpArgs += $dbName
        & "C:\xampp\mysql\bin\mysqldump.exe" @mysqldumpArgs > $backupFile 2>$null

        if ((Test-Path $backupFile) -and (Get-Item $backupFile).Length -gt 0) {
            Write-Log "  تم النسخ الاحتياطي: $backupFile"
            # الاحتفاظ بآخر 10 نسخ فقط، حذف الأقدم — يمنع تراكماً غير محدود بمرور الوقت
            Get-ChildItem $backupDir -Filter "backup_*.sql" | Sort-Object LastWriteTime -Descending |
                Select-Object -Skip 10 | Remove-Item -Force -ErrorAction SilentlyContinue
        } else {
            throw "فشل النسخ الاحتياطي لقاعدة البيانات قبل migrate — تم التوقف عمداً قبل تطبيق أي migration بلا نسخة احتياطية."
        }
    } else {
        Write-Log "  تحذير: تعذّر قراءة DB_DATABASE من .env — تخطّي النسخ الاحتياطي (المتابعة على أي حال)."
    }

    Write-Log "تنفيذ أي جداول جديدة (migrate)..."
    & $phpExe artisan migrate --force 2>&1 | ForEach-Object { Write-Log "  migrate: $_" }

    Write-Log "تنظيف الكاش (config/route/view)..."
    & $phpExe artisan config:clear 2>&1 | Out-Null
    & $phpExe artisan route:clear 2>&1 | Out-Null
    & $phpExe artisan view:clear 2>&1 | Out-Null

    # [مهم] عامل الطابور عملية دائمة تحتفظ بنسخة الكود من وقت آخر تشغيل له في الذاكرة — لا يرى
    # الكود الجديد إطلاقاً بدون إعادة تشغيل فعلية، بخلاف طلبات الويب العادية (OPcache معطّل هنا).
    $queueTask = "WhatsAppLocalSystem-QueueWorker"
    if (Get-ScheduledTask -TaskName $queueTask -ErrorAction SilentlyContinue) {
        Write-Log "إعادة تشغيل عامل الطابور ليطبّق الكود الجديد..."
        Stop-ScheduledTask -TaskName $queueTask -ErrorAction SilentlyContinue
        Start-Sleep -Seconds 2
        Start-ScheduledTask -TaskName $queueTask
    }

    Write-Log "اكتمل التحديث بنجاح: $remoteCommit"
} catch {
    $errorMsg = $_.Exception.Message
    Write-Log "فشل التحديث التلقائي: $errorMsg"

    # تنبيه فوري لصاحب المنشأة عبر واتساب (نفس رقم تنبيهات الطابعات) — بدون هذا، فشل التحديث
    # التلقائي يبقى مخفياً في ملف سجل لا يراجعه أحد إلا عند الاكتشاف بالصدفة لاحقاً.
    try {
        & "C:\xampp\php\php.exe" artisan system:notify-owner "⚠️ فشل التحديث التلقائي للنظام من GitHub.`nالسبب: $errorMsg`nراجع storage/logs/auto-update.log للتفاصيل." 2>&1 | Out-Null
    } catch {
        Write-Log "  تعذّر إرسال تنبيه الفشل عبر واتساب أيضاً: $($_.Exception.Message)"
    }
}
