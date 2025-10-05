#!/bin/bash

echo "Setting up Laravel 12 Docker Environment..."
echo

# Copy environment file
echo "Copying environment file..."
cp .env.docker .env
if [ $? -ne 0 ]; then
    echo "Error: Failed to copy environment file"
    exit 1
fi

# Build and start containers
echo "Building and starting Docker containers..."
docker-compose up -d --build
if [ $? -ne 0 ]; then
    echo "Error: Failed to start Docker containers"
    echo "Make sure Docker Desktop is running"
    exit 1
fi

# Wait a moment for containers to be ready
echo "Waiting for containers to be ready..."
sleep 10

# Install Composer dependencies
echo "Installing Composer dependencies..."
docker-compose exec -T app composer install
if [ $? -ne 0 ]; then
    echo "Warning: Composer install failed, but continuing..."
fi

# Generate application key
echo "Generating application key..."
docker-compose exec -T app php artisan key:generate
if [ $? -ne 0 ]; then
    echo "Warning: Key generation failed, but continuing..."
fi

# Run database migrations
echo "Running database migrations..."
docker-compose exec -T app php artisan migrate
if [ $? -ne 0 ]; then
    echo "Warning: Migration failed, but continuing..."
fi

echo
echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo
echo "Your Laravel application is now running:"
echo "- Laravel App: http://localhost:8000"
echo "- PhpMyAdmin: http://localhost:8080"
echo
echo "Database credentials:"
echo "- Host: localhost:3306"
echo "- Database: laravel12"
echo "- Username: laravel12"
echo "- Password: password"
echo "- Root Password: root"
echo
echo "To stop the containers: docker-compose down"
echo "To view logs: docker-compose logs"
echo
