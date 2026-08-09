@echo off
:: ÅÒÇáÉ ßá ãåÇã/ÎÏãÇÊ ÇáÊÔÛíá ÇáÊáŞÇÆí ÇáÊí ÃäÔÃåÇ 03-Install-AutoStart.bat

net session >nul 2>&1
if %errorLevel% NEQ 0 (
    echo íÊØáÈ åĞÇ ÇáÅÚÏÇÏ ÕáÇÍíÇÊ ÇáãÓÄæá ^(Administrator^)¡ ÓíÊã ØáÈåÇ ÇáÂä...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0setup-autostart.ps1" -Uninstall

echo.
pause
