@echo off
:: إزالة كل مهام/خدمات التشغيل التلقائي التي أنشأها 03-Install-AutoStart.bat

net session >nul 2>&1
if %errorLevel% NEQ 0 (
    echo يتطلب هذا الإعداد صلاحيات المسؤول ^(Administrator^)، سيتم طلبها الآن...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0setup-autostart.ps1" -Uninstall

echo.
pause
