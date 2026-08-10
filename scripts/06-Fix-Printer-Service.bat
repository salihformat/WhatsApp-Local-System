@echo off
chcp 1256 >nul
:: ”ﬂ—»  · €ÌÌ— Õ”«»  ‘€Ì· Œœ„… Apache ·· ⁄—› ⁄·Ï «·ÿ«»⁄…
:: ÌﬁÊ„ »ÿ·» ’·«ÕÌ«  «·„”ƒÊ· À„ «” œ⁄«¡ ”ﬂ—Ì»  «·»«Ê—‘Ì·

net session >nul 2>&1
if %errorLevel% NEQ 0 (
    echo Ã«—Ì ÿ·» ’·«ÕÌ«  «·„”ƒÊ· ^(Administrator^)? Ì—ÃÏ «·„Ê«›ﬁ…...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0fix-printer-service.ps1"

echo.
pause
