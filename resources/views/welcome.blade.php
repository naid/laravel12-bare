<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel 12</title>
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                text-align: center;
                background: white;
                padding: 3rem;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                max-width: 600px;
                margin: 2rem;
            }
            .logo {
                font-size: 4rem;
                color: #e53e3e;
                margin-bottom: 1rem;
            }
            h1 {
                color: #2d3748;
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }
            .version {
                background: #667eea;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 25px;
                display: inline-block;
                margin: 1rem 0;
                font-weight: bold;
            }
            .features {
                text-align: left;
                margin: 2rem 0;
            }
            .feature {
                padding: 0.5rem 0;
                color: #4a5568;
            }
            .feature::before {
                content: "✓";
                color: #48bb78;
                font-weight: bold;
                margin-right: 0.5rem;
            }
            .docker-info {
                background: #f7fafc;
                padding: 1.5rem;
                border-radius: 10px;
                margin-top: 2rem;
                border-left: 4px solid #667eea;
            }
            .docker-info h3 {
                color: #2d3748;
                margin-top: 0;
            }
            .command {
                background: #2d3748;
                color: #e2e8f0;
                padding: 1rem;
                border-radius: 5px;
                font-family: 'Courier New', monospace;
                margin: 0.5rem 0;
                overflow-x: auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">🚀</div>
            <h1>Laravel 12</h1>
            <div class="version">v12.0.0</div>
            
            <p>Welcome to your new Laravel application running in Docker!</p>
            
            <div class="features">
                <div class="feature">PHP 8.3 with Apache</div>
                <div class="feature">MySQL 8.0 Database</div>
                <div class="feature">PhpMyAdmin for database management</div>
                <div class="feature">Laravel 12 Framework</div>
                <div class="feature">Docker Compose setup</div>
            </div>
            
            <div class="docker-info">
                <h3>🐳 Docker Setup</h3>
                <p>Your Laravel application is running in Docker with the following services:</p>
                <div class="command">Laravel App: http://localhost:8000</div>
                <div class="command">PhpMyAdmin: http://localhost:8080</div>
                <div class="command">MySQL: localhost:3306</div>
                
                <p><strong>Next Steps:</strong></p>
                <p>1. Copy <code>.env.docker</code> to <code>.env</code></p>
                <p>2. Run <code>docker-compose up -d</code></p>
                <p>3. Run <code>docker-compose exec app composer install</code></p>
                <p>4. Run <code>docker-compose exec app php artisan key:generate</code></p>
                <p>5. Run <code>docker-compose exec app php artisan migrate</code></p>
            </div>
        </div>
    </body>
</html>
