@echo off
:: ÊÔÛíá ÈäŞÑÉ æÇÍÏÉ: íØáÈ ÕáÇÍíÇÊ ÇáãÓÄæá ÊáŞÇÆíÇğ ÅĞÇ áÒã ÇáÃãÑ¡ Ëã íõÚöÏø ÇáÊÔÛíá ÇáÊáŞÇÆí ÇáßÇãá
:: (Apache + Queue Worker + Scheduler) ÈÍíË íÚãá ÇáäÙÇã ÊáŞÇÆíÇğ ÈÚÏ ßá ÅŞáÇÚ ááÌåÇÒ ÈáÇ Ãí ÎØæÇÊ íÏæíÉ.

net session >nul 2>&1
if %errorLevel% NEQ 0 (
    echo íÊØáÈ åĞÇ ÇáÅÚÏÇÏ ÕáÇÍíÇÊ ÇáãÓÄæá ^(Administrator^)¡ ÓíÊã ØáÈåÇ ÇáÂä...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0setup-autostart.ps1"

echo.
pause
