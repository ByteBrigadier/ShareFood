@echo off
title ShareFood Local Server
echo ========================================================
echo          🚀 Starting ShareFood Web Application
echo ========================================================
echo.

:: Check if MySQL daemon is running on port 3306
netstat -ano | findstr :3306 >nul
if %errorlevel% neq 0 (
    echo [1/2] Starting MySQL database server...
    start /b "" "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe" --datadir="%~dp0mysql_data" --port=3306 --console
    timeout /t 2 >nul
) else (
    echo [1/2] MySQL database server is already running on port 3306.
)

echo [2/2] Starting PHP development server...
echo.
echo --------------------------------------------------------
echo  🌐 App URL: http://localhost:8000
echo  🔑 Demo Credentials:
echo     - Donor:    restaurant@abc.com / password123
echo     - Receiver: ngo@foodbank.com       / password123
echo --------------------------------------------------------
echo.
echo  (Press Ctrl+C to stop the server)
echo.

php -S localhost:8000
