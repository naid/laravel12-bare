# Laravel User Roles and Login System - Implementation Guide

## Overview

This guide will help you implement a user authentication system with role-based access control in your Laravel application.

## Current Setup

- ✅ Users table already exists with: id, name, email, email_verified_at, password, remember_token, timestamps
- ✅ User model is set up and ready

---

## Step-by-Step Implementation

### 1. Create the Roles and User_Roles Tables

#### a) Create roles table migration:

```bash
php artisan make:migration create_roles_table
```

**In the migration, define:**

- `id` (primary key)
- `name` (string, unique) - e.g., "admin", "user", "moderator"
- `description` (text, nullable) - optional description of what the role does
- `timestamps`

**Example structure:**

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

#### b) Create user_roles pivot table (many-to-many relationship):

```bash
php artisan make:migration create_user_roles_table
```

**In the migration, define:**

- `id` (primary key)
- `user_id` (foreign key to users table)
- `role_id` (foreign key to roles table)
- `timestamps`
- Add unique constraint on `user_id` and `role_id` combination (if a user can only have each role once)

**Example structure:**

```php
Schema::create('user_roles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('role_id')->constrained()->onDelete('cascade');
    $table->timestamps();

    // Ensure a user can't have the same role twice
    $table->unique(['user_id', 'role_id']);
});
```

**Run migrations:**

```bash
php artisan migrate
```

---

### 2. Create a Role Model

```bash
php artisan make:model Role
```

**In the Role model (`app/Models/Role.php`), add:**

- Define `$fillable` attributes: `['name', 'description']`
- Add a `users()` relationship method using `belongsToMany(User::class, 'user_roles')`

**Example:**

```php
public function users()
{
    return $this->belongsToMany(User::class, 'user_roles');
}
```

---

### 3. Update the User Model

**In the User model (`app/Models/User.php`), add:**

Add a `roles()` relationship method:

```php
public function roles()
{
    return $this->belongsToMany(Role::class, 'user_roles');
}
```

**Optional but recommended helper methods:**

```php
// Check if user has a specific role
public function hasRole($role)
{
    return $this->roles()->where('name', $role)->exists();
}

// Assign a role to user
public function assignRole($role)
{
    if (is_string($role)) {
        $role = Role::where('name', $role)->firstOrFail();
    }
    return $this->roles()->syncWithoutDetaching($role);
}

// Remove a role from user
public function removeRole($role)
{
    if (is_string($role)) {
        $role = Role::where('name', $role)->firstOrFail();
    }
    return $this->roles()->detach($role);
}
```

---

### 4. Set Up Authentication System

**THIS PROJECT USES LARAVEL SANCTUM** ✅

Laravel Sanctum is already installed in your project. Sanctum provides authentication for:

- **SPA Authentication** - Cookie-based auth for Single Page Applications (Vue/React/Inertia)
- **API Token Authentication** - Token-based auth for mobile apps or third-party APIs

📖 **See the detailed guide:** [`notes/sanctum-authentication-guide.md`](./sanctum-authentication-guide.md)

**Quick overview:**

#### For SPA (Same Domain Frontend):

1. Configure CORS and Sanctum stateful domains
2. Add Sanctum middleware to API routes
3. Create Login/Register controllers that use session authentication
4. Frontend requests CSRF cookie first, then makes authenticated requests

#### For API Token (Mobile/Different Domain):

1. Create Login controller that returns API tokens
2. Use `$user->createToken('name')` to generate tokens
3. Client includes token in `Authorization: Bearer TOKEN` header
4. Tokens stored in `personal_access_tokens` table

**Alternative Options** (if you prefer traditional web authentication):

#### **Option B: Laravel Breeze**

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

#### **Option C: Laravel UI**

```bash
composer require laravel/ui
php artisan ui bootstrap --auth
```

---

### 5. Configure Authentication

Check your `config/auth.php` to ensure:

- The `users` provider is pointing to your User model
- Session guard is configured properly

**Default configuration should look like:**

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

---

### 6. Create Seeders (Optional but Recommended)

Create a seeder to populate default roles:

```bash
php artisan make:seeder RoleSeeder
```

**In the seeder, add your default roles:**

```php
Role::create(['name' => 'admin', 'description' => 'Administrator with full access']);
Role::create(['name' => 'user', 'description' => 'Regular user']);
Role::create(['name' => 'moderator', 'description' => 'Moderator with limited admin access']);
```

**Run the seeder:**

```bash
php artisan db:seed --class=RoleSeeder
```

**Or add it to DatabaseSeeder:**

```php
public function run()
{
    $this->call([
        RoleSeeder::class,
    ]);
}
```

---

### 7. Protect Routes with Middleware

Create custom middleware to check user roles:

```bash
php artisan make:middleware CheckRole
```

**In the middleware (`app/Http/Middleware/CheckRole.php`):**

```php
public function handle(Request $request, Closure $next, ...$roles)
{
    if (!auth()->check()) {
        return redirect('login');
    }

    foreach ($roles as $role) {
        if (auth()->user()->hasRole($role)) {
            return $next($request);
        }
    }

    abort(403, 'Unauthorized action.');
}
```

**Register the middleware in `bootstrap/app.php` (Laravel 11) or `app/Http/Kernel.php` (Laravel 10):**

For Laravel 11:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

**Use it in routes:**

```php
Route::get('/admin', [AdminController::class, 'index'])->middleware('role:admin');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('role:admin,moderator');
```

---

## Quick Reference Commands

```bash
# Create migrations
php artisan make:migration create_roles_table
php artisan make:migration create_user_roles_table

# Create models
php artisan make:model Role

# Run migrations
php artisan migrate

# Create seeder
php artisan make:seeder RoleSeeder

# Run seeder
php artisan db:seed --class=RoleSeeder

# Create middleware
php artisan make:middleware CheckRole

# Install authentication (choose one)
composer require laravel/breeze --dev && php artisan breeze:install
# OR
composer require laravel/ui && php artisan ui bootstrap --auth
```

---

## Testing Your Implementation

1. **Create a test user with a role:**

```php
$user = User::create([
    'name' => 'Test Admin',
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
]);

$user->assignRole('admin');
```

2. **Check if user has role:**

```php
if ($user->hasRole('admin')) {
    // User is an admin
}
```

3. **Get all user roles:**

```php
$roles = $user->roles;
```

---

## Decision Points

Before proceeding, decide:

1. **Can users have multiple roles?**

   - Yes → Use the many-to-many relationship as described
   - No → Consider using a single `role_id` foreign key on users table instead

2. **Which authentication package?**

   - Simple blade templates → Breeze
   - Traditional Bootstrap → Laravel UI
   - Complete custom control → Manual implementation

3. **Role-based or Permission-based?**
   - For now: Role-based (what this guide covers)
   - Later: Consider packages like Spatie Laravel-Permission for complex permission systems

---

## Next Steps

After implementing the basic system:

- Add role assignment in the registration process
- Create admin panel for managing users and roles
- Add more granular permissions if needed
- Implement role-specific dashboards
- Add audit logging for role changes

---

**Last Updated:** October 9, 2025
