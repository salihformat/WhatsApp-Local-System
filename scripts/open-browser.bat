@echo off
timeout /t 5 >nul
cd /d "%~dp0\.."
FOR /F "tokens=1,2 delims==" %%A IN (.env) DO (
    IF "%%A"=="APP_URL" set APP_URL=%%B
)
start "" "%APP_URL%"
