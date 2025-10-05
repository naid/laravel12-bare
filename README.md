# Laravel 12 Docker Setup

This is a Laravel 12 application configured to run with Docker and Docker Compose.

## Features

- **Laravel 12** - Latest Laravel framework
- **PHP 8.3** - Latest PHP version with PHP-FPM
- **Nginx** - High-performance web server
- **MySQL 8.0** - Database server
- **PhpMyAdmin** - Database management interface
- **Docker Compose** - Easy development environment setup

## Services

- **Laravel App**: http://localhost:8000
- **PhpMyAdmin**: http://localhost:8080
- **MySQL**: localhost:3306

## Quick Start

### Prerequisites

- Docker Desktop installed and running
- Git (optional)

### Setup Instructions

1. **Clone or download this project**

2. **Copy environment file**:

   ```bash
   cp .env.docker .env
   ```

3. **Build and start the containers**:

   ```bash
   docker-compose up -d --build
   ```

4. **Install Composer dependencies**:

   ```bash
   docker-compose exec app composer install
   ```

5. **Create bootstrap cache directory** (if needed):

   ```bash
   docker-compose exec app mkdir -p bootstrap/cache
   ```

6. **Generate application key**:

   ```bash
   docker-compose exec app php artisan key:generate
   ```

7. **Run database migrations**:

   ```bash
   docker-compose exec app php artisan migrate
   ```

8. **Access your application**:
   - Laravel App: http://localhost:8000
   - PhpMyAdmin: http://localhost:8080 (root/root)

## Database Configuration

The application is configured to use MySQL with the following settings:

- **Host**: db (Docker service name)
- **Database**: laravel12
- **Username**: laravel12
- **Password**: password
- **Root Password**: root

## Development Commands

### Run Artisan Commands

```bash
docker-compose exec app php artisan [command]
```

### Access Container Shell

```bash
docker-compose exec app bash
```

### View Logs

```bash
docker-compose logs app
docker-compose logs db
```

### Stop Containers

```bash
docker-compose down
```

### Stop and Remove Volumes

```bash
docker-compose down -v
```

## File Structure

```
├── app/                    # Application logic
├── config/                 # Configuration files
├── database/               # Migrations and seeders
├── docker/                 # Docker configuration
│   ├── nginx/              # Nginx configuration
│   └── start.sh            # Startup script
├── public/                 # Web accessible files
├── resources/              # Views, CSS, JS
├── routes/                 # Route definitions
├── storage/                # File storage
├── tests/                  # Test files
├── Dockerfile              # Docker image definition
├── docker-compose.yml      # Docker services
└── composer.json           # PHP dependencies
```

## Next Steps

Once the application is running, you can:

1. Create your first controller: `docker-compose exec app php artisan make:controller ExampleController`
2. Create models: `docker-compose exec app php artisan make:model Example`
3. Create migrations: `docker-compose exec app php artisan make:migration create_examples_table`
4. Add routes in `routes/web.php` or `routes/api.php`

## Troubleshooting

### Bootstrap Cache Directory Error

If you encounter this error during `composer install`:

```
The /var/www/html/bootstrap/cache directory must be present and writable.
```

**Solution:**

```bash
docker-compose exec app mkdir -p bootstrap/cache
docker-compose exec app chown -R www-data:www-data bootstrap/cache
```

### Container won't start

- Make sure Docker Desktop is running
- Check if ports 8000, 8080, and 3306 are available
- Try `docker-compose down -v` and rebuild
- Clear build cache: `docker-compose build --no-cache`

### Composer Install Issues

If `composer install` fails:

```bash
# Clear composer cache and retry
docker-compose exec app composer clear-cache
docker-compose exec app composer install --no-cache
```

### Database connection issues

- Ensure the database container is running: `docker-compose ps`
- Check database logs: `docker-compose logs db`
- Verify environment variables in `.env`
- Wait for database to fully start (may take 30-60 seconds on first run)

### Build Cache Issues

If you see "changes out of order" or file not found errors during build:

```bash
# Stop containers and clear build cache
docker-compose down
docker system prune -f
docker-compose build --no-cache
docker-compose up -d
```

### Permission issues

- On Windows, this is usually handled automatically
- On Linux/Mac, you might need to adjust file permissions:
  ```bash
  docker-compose exec app chown -R www-data:www-data /var/www/html
  docker-compose exec app chmod -R 755 /var/www/html
  ```

### Multiple Laravel Projects

To run multiple Laravel projects simultaneously, ensure each uses different ports:

**Project 1 (Laravel 12):**

- App: `8000:80`
- PhpMyAdmin: `8080:80`
- MySQL: `3306:3306`

**Project 2 (Laravel 11):**

- App: `8001:80`
- PhpMyAdmin: `8081:80`
- MySQL: `3307:3306`

## Environment Variables

Key environment variables in `.env`:

- `APP_NAME`: Application name
- `APP_ENV`: Environment (local, production, etc.)
- `APP_DEBUG`: Debug mode (true/false)
- `DB_HOST`: Database host (use `db` for Docker)
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password

## Support

For Laravel documentation, visit: https://laravel.com/docs
For Docker documentation, visit: https://docs.docker.com/
