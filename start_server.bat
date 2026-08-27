@echo off
echo ========================================
echo  Fleet Management - Starting Server...
echo ========================================
echo.
echo Server akan berjalan di: http://localhost:8080
echo.
echo Login Credentials:
echo   Admin:      admin / password
echo   Approver1:  approver1 / password
echo   Approver2:  approver2 / password
echo.
echo Tekan CTRL+C untuk menghentikan server
echo ========================================
echo.
php -S localhost:8080 public/router.php
