@echo off
chcp 1256 >nul
::  À»Ì  «·»—«„Ã «·Œ«—ÃÌ… «·„ÿ·Ê»… (Node, Composer, SumatraPDF, Tesseract OCR, LibreOffice)

net session >nul 2>&1
if %errorLevel% NEQ 0 (
    echo Ì ÿ·» Â–« «·≈⁄œ«œ ’·«ÕÌ«  «·„”ƒÊ· ^(Administrator^)° ”Ì „ ÿ·»Â« «·¬‰...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

:: [Fix] ≈⁄«œ… «· ‘€Ì· »’·«ÕÌ«  «·„”ƒÊ· √⁄·«Â (Start-Process -Verb RunAs) ﬁœ  »œ√ »„Ã·œ ⁄„· „Œ ·›
:: (€«·»« System32) »œ· „Ã·œ Â–« «·„·› ó ÌÃ» ÷»ÿÂ ’—«Õ… Â‰« „»ﬂ—«° ﬁ»· √Ì ⁄„·Ì… ﬂ «»… „·›« 
:: („À·  ‰“Ì· composer-setup.php √œ‰«Â)° Ê≈·«  ıﬂ » ›Ì „ﬂ«‰ €Ì— „ Êﬁ⁄.
cd /d "%~dp0"

echo.
echo =========================================================
echo   À»Ì  «·„ ÿ·»«  «·Œ«’… »»Ì∆… «· ÿÊÌ— (Node.js ^& Composer)
echo =========================================================
echo [1/3] ≈÷«›… „”«— PHP ≈·Ï «·‰Ÿ«„ (≈‰ ·„ Ìﬂ‰ „ÊÃÊœ« „”»ﬁ«)...
:: [Fix] «” Œœ«„ setx · ⁄œÌ· PATH Œÿ— Â‰«: setx Ìﬁ ÿ⁄ «·ﬁÌ„… »’„  ⁄‰œ  Ã«Ê“Â« 1024 Õ—›«° Ê%PATH%
:: Â‰« ÂÊ „”«— «·⁄„·Ì… «·„ıœ„ÛÃ (System+User „⁄«° €«·»« √ÿÊ· „‰ –·ﬂ)° ›ﬂ «» Â ﬂ«„·« ﬂ„”«—
:: System «·ÃœÌœ ﬁœ Ìı ·› „”«— «·ÃÂ«“ ›⁄·Ì« ( Õﬁ¯ﬁ ›⁄·Ì:  Ã«Ê“ 1024 Õ—›« ⁄·Ï √ÃÂ“… ⁄«œÌ… ﬂÀÌ—…).
:: «·»œÌ· «·¬„‰: PowerShell (·« Ìﬁ ÿ⁄ «·ﬁÌ„…)° „⁄ ﬁ—«¡… „”«— System «·›⁄·Ì ›ﬁÿ (Ê·Ì” «·„ıœ„ÛÃ)
:: Ê≈÷«›… «·„”«— «·ÃœÌœ ›ﬁÿ ≈‰ ·„ Ìﬂ‰ „ÊÃÊœ« √’·« (Ì„‰⁄ √Ì÷«  ﬂ—«—Â ⁄‰œ ≈⁄«œ…  ‘€Ì· «·”ﬂ—» ).
powershell -NoProfile -Command "$p = [Environment]::GetEnvironmentVariable('Path','Machine'); if ($p -notlike '*C:\xampp\php*') { [Environment]::SetEnvironmentVariable('Path', $p + ';C:\xampp\php', 'Machine') }"

echo [2/3]  À»Ì  Node.js (’«„ «)...
winget install -e --id OpenJS.NodeJS --accept-package-agreements --accept-source-agreements --silent

echo [3/3]  À»Ì  Composer (’«„ «)...
:: [Fix] ·«  ÊÃœ Õ“„… winget —”„Ì… ·‹ Composer ( Õﬁ¯ﬁ ›⁄·Ì: "Composer.Composer" €Ì— „ÊÃÊœ ≈ÿ·«ﬁ«°
:: ÊÌ‰ÃÕ winget install »’„  —€„ ›‘·Â «·›⁄·Ì ·√‰ «·ﬂÊœ ·« Ìı›Õ’° ›Ì›‘· composer install ·«Õﬁ« »·«
::  ›”Ì— Ê«÷Õ). «·»œÌ·: ÿ—Ìﬁ… «· À»Ì  «·’«„  «·—”„Ì… «·„ÊÀ¯ﬁ… „‰ getcomposer.org ‰›”Â« (composer-
:: setup.php)°  ı‰ Ã composer.phar + €·«› composer.bat Ì” œ⁄ÌÂ° À„ Ìı÷«› „Ã·œÂ ·„”«— Machine.
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
echo   À»Ì  »—«„Ã «·ÿ»«⁄… «·–ﬂÌ… Ê«·‹ OCR...
echo =========================================================
cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install-prerequisites.ps1"

echo.
echo ==================================================
echo  „ «· À»Ì  »«·ﬂ«„·! 
echo **„·«ÕŸ… Â«„… Ãœ«**: ÌÃ» ≈€·«ﬁ Â–Â «·‰«›–… (CMD) Ê› ÕÂ« „‰ ÃœÌœ Õ Ï Ì ⁄—› «·ÊÌ‰œÊ“ ⁄·Ï √œÊ«  Node Ê Composer «· Ì  „  À»Ì Â« ·· Ê.
echo ==================================================
pause
