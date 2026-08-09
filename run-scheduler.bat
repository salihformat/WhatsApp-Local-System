@echo off
REM ÇáÇäÊÞÇá Åáì ãÌáÏ ÇáãÔÑæÚ
cd /d "C:\xampp\htdocs\whatsapp-local-system"

REM ÖÈØ ãÓÇÑ PHP Ýí ÍÇá áã íßä ãÖÇÝÇð (ÅÐÇ áÒã ÇáÃãÑ)
REM set PATH=%PATH%;C:\xampp\php

REM ÊÔÛíá ãÌÏæá ãåÇã Laravel ãÍáíÇð
php artisan schedule:run >> storage\logs\scheduler_local.log 2>&1
