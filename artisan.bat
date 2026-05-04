@echo off
REM Windows batch file to run PHP artisan commands

setlocal enabledelayedexpansion

set PHP_PATH=C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe

if not exist "%PHP_PATH%" (
    echo PHP not found at %PHP_PATH%
    exit /b 1
)

"%PHP_PATH%" artisan %*
