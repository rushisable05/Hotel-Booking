@echo off
setlocal
cd /d "%~dp0"

echo ============================================
echo   Hotel Booking System - Safe Launcher
echo ============================================

set "PHP_BIN="
if exist "C:\xampp\php\php.exe" set "PHP_BIN=C:\xampp\php\php.exe"
if "%PHP_BIN%"=="" for %%I in (php.exe) do set "PHP_BIN=%%~$PATH:I"

if "%PHP_BIN%"=="" (
    echo [ERROR] PHP not found! Please install XAMPP or Laragon.
    pause
    exit /b 1
)

REM Start MySQL if not already running
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I "mysqld.exe" >NUL
if errorlevel 1 (
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        echo [1/4] Starting XAMPP MySQL...
        start "Hotel-Booking MySQL" /MIN "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
        timeout /t 3 /nobreak >NUL
    ) else (
        echo [WARN] mysqld.exe not running and XAMPP MySQL not found at C:\xampp. Make sure your MySQL service is running.
    )
) else (
    echo [1/4] MySQL is already running.
)

REM Auto-restore DB if missing (e.g. after XAMPP reinstall)
echo [2/4] Checking database health...
"%PHP_BIN%" "%~dp0database\restore-db.php" --if-missing

REM Start phpMyAdmin on port 8080 if available
if exist "C:\xampp\phpMyAdmin\index.php" (
    echo [3/4] Starting phpMyAdmin on http://localhost:8080 ...
    start "Hotel-Booking phpMyAdmin" /MIN "%PHP_BIN%" -S localhost:8080 -t "C:\xampp\phpMyAdmin"
) else (
    echo [3/4] phpMyAdmin folder not found, skipping port 8080.
)

echo [4/4] Starting Hotel Booking Website on http://localhost:8000 ...
echo.
echo   Website:    http://localhost:8000
echo   Admin:      http://localhost:8000/admin/login.php
echo   phpMyAdmin: http://localhost:8080
echo.
echo Press Ctrl+C in this window to stop the website server.
start "" "http://localhost:8000"
"%PHP_BIN%" -S localhost:8000
