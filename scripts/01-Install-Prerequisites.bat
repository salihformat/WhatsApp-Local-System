@echo off
chcp 65001 >nul
:: تثبيت البرامج الخارجية المطلوبة (Node, Composer, SumatraPDF, Tesseract OCR, LibreOffice)

net session >nul 2>&1
if %errorLevel% NEQ 0 (
    echo يتطلب هذا الإعداد صلاحيات المسؤول ^(Administrator^)، سيتم طلبها الآن...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

:: [Fix] إعادة التشغيل بصلاحيات المسؤول أعلاه (Start-Process -Verb RunAs) قد تبدأ بمجلد عمل مختلف
:: (غالباً System32) بدل مجلد هذا الملف — يجب ضبطه صراحة هنا مبكراً، قبل أي عملية كتابة ملفات
:: (مثل تنزيل composer-setup.php أدناه)، وإلا تُكتب في مكان غير متوقع.
cd /d "%~dp0"

echo.
echo =========================================================
echo  تثبيت المتطلبات الخاصة ببيئة التطوير (Node.js ^& Composer)
echo =========================================================
echo [1/3] إضافة مسار PHP إلى النظام (إن لم يكن موجوداً مسبقاً)...
:: [Fix] استخدام setx لتعديل PATH خطر هنا: setx يقتطع القيمة بصمت عند تجاوزها 1024 حرفاً، و%PATH%
:: هنا هو مسار العملية المُدمَج (System+User معاً، غالباً أطول من ذلك)، فكتابته كاملاً كمسار
:: System الجديد قد يُتلف مسار الجهاز فعلياً (تحقّق فعلي: تجاوز 1024 حرفاً على أجهزة عادية كثيرة).
:: البديل الآمن: PowerShell (لا يقتطع القيمة)، مع قراءة مسار System الفعلي فقط (وليس المُدمَج)
:: وإضافة المسار الجديد فقط إن لم يكن موجوداً أصلاً (يمنع أيضاً تكراره عند إعادة تشغيل السكربت).
powershell -NoProfile -Command "$p = [Environment]::GetEnvironmentVariable('Path','Machine'); if ($p -notlike '*C:\xampp\php*') { [Environment]::SetEnvironmentVariable('Path', $p + ';C:\xampp\php', 'Machine') }"

echo [2/3] تثبيت Node.js (صامتاً)...
winget install -e --id OpenJS.NodeJS --accept-package-agreements --accept-source-agreements --silent

echo [3/3] تثبيت Composer (صامتاً)...
:: [Fix] لا توجد حزمة winget رسمية لـ Composer (تحقّق فعلي: "Composer.Composer" غير موجود إطلاقاً،
:: وينجح winget install بصمت رغم فشله الفعلي لأن الكود لا يُفحص، فيفشل composer install لاحقاً بلا
:: تفسير واضح). البديل: طريقة التثبيت الصامت الرسمية الموثّقة من getcomposer.org نفسها (composer-
:: setup.php)، تُنتج composer.phar + غلاف composer.bat يستدعيه، ثم يُضاف مجلده لمسار Machine.
set "COMPOSER_BIN_DIR=C:\ComposerBin"
if not exist "%COMPOSER_BIN_DIR%" mkdir "%COMPOSER_BIN_DIR%"
C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
C:\xampp\php\php.exe composer-setup.php --install-dir="%COMPOSER_BIN_DIR%" --filename=composer.phar
del composer-setup.php
(
    echo @echo off
    echo C:\xampp\php\php.exe "%%~dp0composer.phar" %%*
) > "%COMPOSER_BIN_DIR%\composer.bat"
powershell -NoProfile -Command "$p = [Environment]::GetEnvironmentVariable('Path','Machine'); if ($p -notlike '*C:\ComposerBin*') { [Environment]::SetEnvironmentVariable('Path', $p + ';C:\ComposerBin', 'Machine') }"

echo.
echo =========================================================
echo  تثبيت برامج الطباعة الذكية والـ OCR...
echo =========================================================
cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install-prerequisites.ps1"

echo.
echo ==================================================
echo تم التثبيت بالكامل! 
echo **ملاحظة هامة جداً**: يجب إغلاق هذه النافذة (CMD) وفتحها من جديد حتى يتعرف الويندوز على أدوات Node و Composer التي تم تثبيتها للتو.
echo ==================================================
pause
