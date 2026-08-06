<#
    تثبيت البرامج الخارجية المطلوبة لميزات النظام (الطباعة الذكية وOCR) تلقائياً عبر winget، على
    جهاز جديد لا تحتوي بيئته على هذه البرامج بعد. كل برنامج يُتحقق من وجوده أولاً (بالمسار الذي
    يتوقعه ملف .env) ويُتخطّى إن كان مثبَّتاً مسبقاً — آمن لإعادة التشغيل عدة مرات.

    ملاحظة: Ghostscript غير مُدرَج هنا عمداً — لا يوجد له حزمة winget رسمية موثوقة (تحقّق فعلي)،
    ورابط تنزيله المباشر مرتبط برقم إصدار يتغيّر باستمرار مما يجعل أتمتته هشة. يبقى تثبيته يدوياً
    فقط (راجع القسم 5 بصفحة /docs) — وهو أصلاً "حل أخير" اختياري لحالة نادرة (PDF بطبقة نص تالفة).

    الاستخدام: شغّل Install-Prerequisites.bat (سيطلب صلاحيات المسؤول تلقائياً)، أو نفّذ هذا الملف
    مباشرة من PowerShell كمسؤول: .\install-prerequisites.ps1
#>

param(
    [string]$ProjectPath = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
)

$ErrorActionPreference = "Stop"

function Write-Step($msg) { Write-Host "==> $msg" -ForegroundColor Cyan }
function Write-Ok($msg)   { Write-Host "   $msg" -ForegroundColor Green }
function Write-Warn($msg) { Write-Host "   تحذير: $msg" -ForegroundColor Yellow }

if (-not ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    throw "يجب تشغيل هذا السكربت بصلاحيات المسؤول (Administrator). استخدم Install-Prerequisites.bat بدلاً من ذلك."
}

if (!(Get-Command winget -ErrorAction SilentlyContinue)) {
    throw "winget غير متوفر على هذا الجهاز (يحتاج Windows 10 1709+ أو Windows 11 مع App Installer من Microsoft Store). ثبّت البرامج يدوياً بدلاً من ذلك — راجع القسم 5 بصفحة /docs."
}

$sumatraExpectedPath = "C:\SumatraPDF\SumatraPDF.exe"
$tesseractExpectedPath = "C:\Program Files\Tesseract-OCR\tesseract.exe"
$libreOfficeExpectedPath = "C:\Program Files\LibreOffice\program\soffice.exe"

# 1) SumatraPDF — لطباعة PDF/الصور بصمت
Write-Step "SumatraPDF..."
if (Test-Path $sumatraExpectedPath) {
    Write-Ok "موجود مسبقاً في $sumatraExpectedPath — تخطّي."
} else {
    Write-Host "   جاري التثبيت عبر winget..."
    winget install --id SumatraPDF.SumatraPDF --silent --accept-package-agreements --accept-source-agreements | Out-Null

    # winget يُثبّت SumatraPDF بمسار قياسي مختلف عن المسار الذي يتوقعه .env (C:\SumatraPDF\) —
    # نبحث عن النسخة المُثبَّتة فعلياً وننسخها لنفس المسار المتوقع بدل تعديل .env، حفاظاً على
    # نفس التعليمات الموثَّقة في صفحة /docs لكل من ثبَّته يدوياً سابقاً.
    $foundExe = Get-ChildItem -Path @(
        "$env:LOCALAPPDATA\SumatraPDF",
        "C:\Program Files\SumatraPDF",
        "C:\Program Files (x86)\SumatraPDF"
    ) -Filter "SumatraPDF.exe" -ErrorAction SilentlyContinue -Recurse | Select-Object -First 1

    if ($foundExe) {
        New-Item -ItemType Directory -Path "C:\SumatraPDF" -Force | Out-Null
        Copy-Item -Path $foundExe.FullName -Destination $sumatraExpectedPath -Force
        Write-Ok "تم التثبيت والنسخ إلى $sumatraExpectedPath"
    } else {
        Write-Warn "تم التثبيت عبر winget لكن تعذّر تحديد مكانه تلقائياً. ابحث عن SumatraPDF.exe يدوياً وانسخه إلى $sumatraExpectedPath (أو حدّث SUMATRA_PDF_PATH في .env لمساره الفعلي)."
    }
}

# 2) Tesseract OCR — لقراءة النصوص من الصور والمستندات الممسوحة ضوئياً
Write-Step "Tesseract OCR..."
if (Test-Path $tesseractExpectedPath) {
    Write-Ok "موجود مسبقاً في $tesseractExpectedPath — تخطّي."
} else {
    Write-Host "   جاري التثبيت عبر winget..."
    winget install --id UB-Mannheim.TesseractOCR --silent --accept-package-agreements --accept-source-agreements | Out-Null

    if (Test-Path $tesseractExpectedPath) {
        Write-Ok "تم التثبيت في $tesseractExpectedPath"
    } else {
        Write-Warn "تم التثبيت عبر winget لكن المسار المتوقع $tesseractExpectedPath غير موجود. تحقّق يدوياً وحدّث TESSERACT_BIN_PATH في .env إن لزم."
    }
}

# تحقّق منفصل من حزمة اللغة العربية — المثبِّت الصامت لا يضمن دائماً تضمينها (تحتاج اختياراً
# صريحاً أثناء التثبيت التفاعلي العادي)، وبدونها تفشل قراءة المستندات العربية تحديداً عبر OCR
$tessdataPath = "C:\Program Files\Tesseract-OCR\tessdata\ara.traineddata"
if ((Test-Path $tesseractExpectedPath) -and !(Test-Path $tessdataPath)) {
    Write-Warn "حزمة اللغة العربية (ara.traineddata) غير موجودة! التثبيت الصامت لا يضمن تضمينها. نزّلها يدوياً من https://github.com/tesseract-ocr/tessdata وضعها في: C:\Program Files\Tesseract-OCR\tessdata\"
}

# 3) LibreOffice — لتحويل ملفات Word/Excel/PowerPoint إلى PDF قبل الطباعة
Write-Step "LibreOffice..."
if (Test-Path $libreOfficeExpectedPath) {
    Write-Ok "موجود مسبقاً في $libreOfficeExpectedPath — تخطّي."
} else {
    Write-Host "   جاري التثبيت عبر winget (قد يستغرق عدة دقائق، ~300 ميجابايت)..."
    winget install --id TheDocumentFoundation.LibreOffice --silent --accept-package-agreements --accept-source-agreements | Out-Null

    if (Test-Path $libreOfficeExpectedPath) {
        Write-Ok "تم التثبيت في $libreOfficeExpectedPath"
    } else {
        Write-Warn "تم التثبيت عبر winget لكن المسار المتوقع $libreOfficeExpectedPath غير موجود. تحقّق يدوياً وحدّث LIBREOFFICE_PATH في .env إن لزم."
    }
}

# 4) Ghostscript — غير مُؤتمت، راجع الملاحظة أعلى الملف
Write-Step "Ghostscript (تذكير)"
Write-Host "   Ghostscript لا يُثبَّت تلقائياً هنا (لا توجد حزمة winget موثوقة له). إن احتجته فعلاً" -ForegroundColor Yellow
Write-Host "   (حالة نادرة: PDF ممسوح ضوئياً بطبقة نص تالفة)، ثبّته يدوياً من https://ghostscript.com/releases/gsdnld.html" -ForegroundColor Yellow
Write-Host "   ثم حدّث GHOSTSCRIPT_BIN_PATH في .env بمساره الفعلي (يتضمّن رقم الإصدار، يتغيّر مع كل تحديث)." -ForegroundColor Yellow

Write-Host ""
Write-Host "انتهى فحص/تثبيت البرامج الخارجية. راجع أي تحذيرات أعلاه إن وُجدت." -ForegroundColor Green
Write-Host "الخطوة التالية: شغّل Install-AutoStart.bat لإعداد التشغيل التلقائي الكامل." -ForegroundColor Green
