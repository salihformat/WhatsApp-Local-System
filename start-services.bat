@echo off
title WhatsApp Local System Services
echo ===================================================
echo    Starting WhatsApp Local System Services...
echo ===================================================

echo [1/2] Starting Queue Worker (for sending messages)...
start "WhatsApp Queue Worker" cmd /c "c:\xampp\php\php.exe artisan queue:work"

echo [2/2] Starting Scheduler (for scanning PrintMonitor)...
c:\xampp\php\php.exe artisan schedule:work
