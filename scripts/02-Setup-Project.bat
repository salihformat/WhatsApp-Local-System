@echo off
chcp 65001 >nul
echo ==================================================
echo  إعداد وتجهيز المشروع (Setup Project)
echo ==================================================
echo.

cd /d "%~dp0\.."

echo [1/7] تجهيز ملف البيئة .env ...
if not exist .env (
    copy .env.example .env
    echo تم نسخ .env بنجاح.
) else (
    echo ملف .env موجود مسبقاً.
)

echo.
echo [2/7] تثبيت حزم PHP (Composer)...
call composer install

echo.
echo [3/7] إنشاء مفتاح التطبيق...
call C:\xampp\php\php.exe artisan key:generate

echo.
echo [4/7] إنشاء قاعدة البيانات...
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS whatsapp_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
if %errorLevel% neq 0 (
    echo [ERROR] حدث خطأ أثناء الاتصال بقاعدة البيانات. تأكد من أن خدمة MySQL في XAMPP قيد التشغيل.
    pause
    exit /b 1
)

echo.
echo [5/7] تنفيذ الجداول (Migrations)...
call C:\xampp\php\php.exe artisan migrate --force

echo.
echo [6/7] ربط مجلد التخزين العام (Storage Link)...
:: [Fix] كان مفقوداً — المشروع يعتمد فعلياً على هذا الرابط لعرض مرفقات واتساب (صور/ملفات) عبر
:: Storage::disk('public')->url()/asset('storage/...')، وبدونه تظهر روابط الملفات معطوبة (404).
call C:\xampp\php\php.exe artisan storage:link

echo.
echo [7/7] تثبيت وبناء الواجهات (NPM)...
call npm install
call npm run build

echo.
echo ==================================================
echo تم إعداد المشروع وبناء الواجهات بنجاح!
echo ==================================================
pause
