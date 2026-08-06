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
    Write-Log "فشل التحديث التلقائي: $($_.Exception.Message)"
}
