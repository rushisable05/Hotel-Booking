@echo off
setlocal
cd /d "%~dp0"

set "PHP_BIN="
if exist "C:\xampp\php\php.exe" set "PHP_BIN=C:\xampp\php\php.exe"
if "%PHP_BIN%"=="" if exist "C:\laragon\bin\php\php.exe" set "PHP_BIN=C:\laragon\bin\php\php.exe"
if "%PHP_BIN%"=="" for %%I in (php.exe) do set "PHP_BIN=%%~$PATH:I"

if "%PHP_BIN%"=="" (
    echo [ERROR] PHP not found. Install XAMPP/Laragon or add php.exe to PATH.
    pause
    exit /b 1
)

echo Restoring Hotel Booking Database from latest project backup...
"%PHP_BIN%" "%~dp0database\restore-db.php" %1
echo.
pause
