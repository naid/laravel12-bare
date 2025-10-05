@echo off
echo Setting up Laravel 12 Docker Environment...
echo.

REM Copy environment file
echo Copying environment file...
copy .env.docker .env
if %errorlevel% neq 0 (
    echo Error: Failed to copy environment file
    pause
    exit /b 1
)

REM Build and start containers
echo Building and starting Docker containers...
docker-compose up -d --build
if %errorlevel% neq 0 (
    echo Error: Failed to start Docker containers
    echo Make sure Docker Desktop is running
    pause
    exit /b 1
)

REM Wait a moment for containers to be ready
echo Waiting for containers to be ready...
timeout /t 10 /nobreak >nul

REM Install Composer dependencies
echo Installing Composer dependencies...
docker-compose exec -T app composer install
if %errorlevel% neq 0 (
    echo Warning: Composer install failed, but continuing...
)

REM Generate application key
echo Generating application key...
docker-compose exec -T app php artisan key:generate
if %errorlevel% neq 0 (
    echo Warning: Key generation failed, but continuing...
)

REM Run database migrations
echo Running database migrations...
docker-compose exec -T app php artisan migrate
if %errorlevel% neq 0 (
    echo Warning: Migration failed, but continuing...
)

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Your Laravel application is now running:
echo - Laravel App: http://localhost:8000
echo - PhpMyAdmin: http://localhost:8080
echo.
echo Database credentials:
echo - Host: localhost:3306
echo - Database: laravel12
echo - Username: laravel12
echo - Password: password
echo - Root Password: root
echo.
echo To stop the containers: docker-compose down
echo To view logs: docker-compose logs
echo.
pause
