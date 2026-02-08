@echo off
REM Setup script for girlfriend_surprise project (Windows)

echo.
echo 🎁 Girlfriend Surprise - Setup Script (Windows)
echo =============================================
echo.

REM Check if .env exists
if not exist ".env" (
    echo ✨ Creating .env from .env.example...
    copy .env.example .env
    echo 📝 Please edit .env with your database credentials
) else (
    echo ✅ .env already exists
)

echo.
echo 🐳 Checking Docker...
where docker >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ Docker found
    echo.
    echo Starting MySQL + phpMyAdmin...
    docker compose up -d
    echo ✅ Services started!
    echo    phpMyAdmin: http://localhost:8080
    echo    Credentials: root / example_root_password
) else (
    echo ⚠️  Docker not found - install from https://www.docker.com/
    echo.
    echo 🔧 Alternatively, using XAMPP MySQL:
    echo    1. Start XAMPP MySQL service
    echo    2. Open phpMyAdmin at http://localhost/phpmyadmin
    echo    3. Create database 'girlfriend_surprise'
    echo    4. Import init.sql file
)

echo.
echo 📂 Checking folder permissions...
if not exist "img" (
    mkdir img
    echo ✅ Created img\ folder
) else (
    echo ✅ img\ folder exists
)

if not exist "data" (
    mkdir data
    echo ✅ Created data\ folder
) else (
    echo ✅ data\ folder exists
)

echo.
echo 🎉 Setup complete!
echo.
echo Next steps:
echo 1. Open http://localhost/girlfriend_surprise/index.php in browser
echo 2. Add photos to img\ folder or use 'Add Picture' button
echo 3. Customize messages in index.php
echo 4. Share with your special someone! 💕
echo.
echo Need help? See README.md for detailed instructions
echo.
pause
