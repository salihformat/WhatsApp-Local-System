@echo off
chcp 1256 >nul
cd /d "%~dp0"
echo ==================================================
echo          ‘€Ì· «·‰›ﬁ «·”—Ì⁄ ⁄»— Cloudflare
echo ==================================================
echo.

if not exist "cloudflared.exe" goto download_cf
goto start_tunnel

:download_cf
echo Ã«—Ì  Õ„Ì· √œ«… Cloudflared „Ã«‰« ·√Ê· „—…...
powershell -NoProfile -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe' -OutFile 'cloudflared.exe'"
if not exist "cloudflared.exe" goto download_failed

:start_tunnel
echo.
echo »œ¡ «·« ’«· »ŒÊ«œ„ Cloudflare...
echo «‰”Œ «·—«»ÿ «·–Ì ”Ì‰ ÂÌ »‹ trycloudflare.com
echo · ⁄ÿÌÂ ·√Ì ‘Œ’ ··œŒÊ· ≈·Ï «·‰Ÿ«„!
echo ( ‰»ÌÂ: Â–« «·—«»ÿ Ì €Ì— ﬂ·„« √⁄œ   ‘€Ì· Â–Â «·‰«›–…)
echo.
cloudflared.exe tunnel --url http://localhost:8006
pause
exit /b 0

:download_failed
echo.
echo [ERROR] ›‘·  Õ„Ì· √œ«… Cloudflared. Ì—ÃÏ «· Õﬁﬁ „‰ « ’«·ﬂ »«·≈‰ —‰ .
pause
exit /b 1
