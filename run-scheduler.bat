@echo off
REM تعيين مسار المشروع المحلي
cd /d "C:\xampp\htdocs\whatsapp-local-system"

REM تحديد متغيرات البيئة إذا لزم الأمر (إذا لم يكن PHP في PATH)
REM set PATH=%PATH%;C:\xampp\php

REM تشغيل Laravel Scheduler للنظام المحلي مع تسجيل النتائج
php artisan schedule:run >> storage\logs\scheduler_local.log 2>&1

REM إضافة timestamp للسجل
echo [%date% %time%] Local System Scheduler executed >> storage\logs\scheduler_local.log

REM التحقق من وجود أخطاء وإرسال تنبيه إذا لزم الأمر
if %ERRORLEVEL% NEQ 0 (
    echo [%date% %time%] ERROR: Local Scheduler failed with exit code %ERRORLEVEL% >> storage\logs\scheduler_local_error.log
    REM يمكن إضافة أمر لإرسال تنبيه عبر البريد الإلكتروني هنا
)

REM إضافة فاصل في السجل لسهولة القراءة
echo ---------------------------------------- >> storage\logs\scheduler_local.log

