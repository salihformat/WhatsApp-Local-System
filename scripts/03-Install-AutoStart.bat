@echo off
:: تشغيل بنقرة واحدة: يطلب صلاحيات المسؤول تلقائياً إذا لزم الأمر، ثم يُعِدّ التشغيل التلقائي الكامل
:: (Apache + Queue Worker + Scheduler) بحيث يعمل النظام تلقائياً بعد كل إقلاع للجهاز بلا أي خطوات يدوية.

net session >nul 2>&1
if %errorLevel% NEQ 0 (
    echo يتطلب هذا الإعداد صلاحيات المسؤول ^(Administrator^)، سيتم طلبها الآن...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0setup-autostart.ps1"

echo.
pause
