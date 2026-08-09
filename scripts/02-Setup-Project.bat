@echo off
chcp 1256 >nul
echo ==================================================
echo  ≈⁄œ«œ Ê ÃÂÌ“ «·„‘—Ê⁄ (Setup Project)
echo ==================================================
echo.

cd /d "%~dp0\.."

echo [1/8]  ÃÂÌ“ „·› «·»Ì∆… .env ...
if not exist .env copy .env.example .env

echo.
echo [2/8]  ›⁄Ì· ≈÷«›«  PHP «·„ÿ·Ê»… (GD, ZIP)  ·ﬁ«∆Ì«...
powershell -NoProfile -Command "$ini = 'C:\xampp\php\php.ini'; if (Test-Path $ini) { $c = Get-Content $ini; $c = $c -replace '(?m)^;extension=gd', 'extension=gd' -replace '(?m)^;extension=zip', 'extension=zip'; [System.IO.File]::WriteAllText($ini, ($c -join \"`r`n\"), [System.Text.Encoding]::UTF8) }"

echo.
echo [3/8]  À»Ì  Õ“„ PHP (Composer)...
call composer install
if %errorLevel% neq 0 goto composer_error

echo.
echo [4/8] ≈‰‘«¡ „› «Õ «· ÿ»Ìﬁ...
call C:\xampp\php\php.exe artisan key:generate

echo.
echo [5/8] ≈‰‘«¡ ﬁ«⁄œ… «·»Ì«‰« ...
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS whatsapp_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
if %errorLevel% neq 0 goto mysql_error

echo.
echo [6/8]  ‰›Ì– «·Ãœ«Ê· (Migrations)...
call C:\xampp\php\php.exe artisan migrate --force --seed

echo.
echo [7/8] —»ÿ „Ã·œ «· Œ“Ì‰ «·⁄«„ (Storage Link)...
call C:\xampp\php\php.exe artisan storage:link

echo.
echo [8/8]  À»Ì  Ê»‰«¡ «·Ê«ÃÂ«  (NPM)...
call npm install
call npm run build

echo.
echo ==================================================
echo  „ ≈⁄œ«œ «·„‘—Ê⁄ Ê»‰«¡ «·Ê«ÃÂ«  »‰Ã«Õ!
echo ==================================================
echo ==================================================
echo              »Ì«‰«  «·œŒÊ· «·«› —«÷Ì…
echo ==================================================
echo  [«·„œÌ— - Admin]
echo  «·»—Ìœ: admin@rasayily.com
echo  ﬂ·„… «·„—Ê—: admin123
echo.
echo  [«·„” Œœ„ «·⁄«œÌ - User]
echo  «·»—Ìœ: user@rasayily.com
echo  ﬂ·„… «·„—Ê—: user123
echo ==================================================
pause


:: Open Browser automatically
FOR /F "tokens=1,2 delims==" %%A IN (.env) DO (
    IF "%%A"=="APP_URL" set APP_URL=%%B
)
start "" "%APP_URL%"
exit /b 0

:composer_error
echo.
echo [ERROR] ›‘·  À»Ì  Õ“„ Composer! Ì—ÃÏ „—«Ã⁄… «·√Œÿ«¡ √⁄·«Â.
pause
exit /b 1

:mysql_error
echo.
echo [ERROR] ÕœÀ Œÿ√ √À‰«¡ «·« ’«· »ﬁ«⁄œ… «·»Ì«‰« .  √ﬂœ „‰ √‰ Œœ„… MySQL ﬁÌœ «· ‘€Ì·.
pause
exit /b 1
