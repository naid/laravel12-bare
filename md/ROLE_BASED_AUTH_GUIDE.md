# Role-Based Authentication System Guide

This guide will teach you how to create a complete authentication system with three user roles: **Admin**, **Manager**, and **User**.

## 📋 What You'll Build

- ✅ Login page with authentication
- ✅ Three user roles (Admin, Manager, User)
- ✅ Role-based dashboard access
- ✅ Middleware for role protection
- ✅ Different dashboard views per role

---

## Part 1: Database Setup

### Step 1: Create Migration to Add Role Column

First, create a migration to add the `role` field to the `users` table:

```bash
docker exec laravel12_app php artisan make:migration add_role_to_users_table
```

This creates a new file in `database/migrations/` with a timestamp.

### Step 2: Edit the Migration

Open the newly created migration file and add the `role` column:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column after email
            // Default: 'user' - ensures new users are regular users by default
            // Options: 'admin', 'manager', 'user'
            $table->enum('role', ['admin', 'manager', 'user'])
                  ->default('user')
                  ->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
```

**Understanding the Code:**

- `enum('role', [...])` - Creates a column that can only have specific values
- `->default('user')` - New users are 'user' role by default
- `->after('email')` - Places the column after the email column
- `down()` method - Reverts the migration (removes the column)

### Step 3: Run the Migration

```bash
docker exec laravel12_app php artisan migrate
```

**What this does:** Adds the `role` column to your `users` table in the database.

---

## Part 2: Update User Model

### Step 1: Add Role to Fillable Array

Open `app/Models/User.php` and add `role` to the `$fillable` array:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',  // Add this line
];
```

**Why?** This allows mass assignment of the role field when creating users.

### Step 2: Add Role Constants (Optional but Recommended)

Add these constants at the top of the User class:

```php
class User extends Authenticatable
{
    // Role constants for easy reference
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_USER = 'user';

    // ... rest of the class
```

**Why?** Using constants prevents typos. Instead of typing `'admin'` everywhere, use `User::ROLE_ADMIN`.

### Step 3: Add Helper Methods

Add these helpful methods to check user roles:

```php
/**
 * Check if user is an admin
 */
public function isAdmin(): bool
{
    return $this->role === self::ROLE_ADMIN;
}

/**
 * Check if user is a manager
 */
public function isManager(): bool
{
    return $this->role === self::ROLE_MANAGER;
}

/**
 * Check if user is a regular user
 */
public function isUser(): bool
{
    return $this->role === self::ROLE_USER;
}

/**
 * Check if user has a specific role
 */
public function hasRole(string $role): bool
{
    return $this->role === $role;
}

/**
 * Check if user has any of the given roles
 */
public function hasAnyRole(array $roles): bool
{
    return in_array($this->role, $roles);
}
```

**Why?** These methods make your code cleaner:

- ❌ `if (Auth::user()->role === 'admin')`
- ✅ `if (Auth::user()->isAdmin())`

---

## Part 3: Create Test Users

### Using Tinker

Create users for each role to test:

```bash
docker exec laravel12_app php artisan tinker
```

Then run these commands:

```php
// Admin user
\App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
]);

// Manager user
\App\Models\User::create([
    'name' => 'Manager User',
    'email' => 'manager@example.com',
    'password' => bcrypt('password'),
    'role' => 'manager',
]);

// Regular user
\App\Models\User::create([
    'name' => 'Regular User',
    'email' => 'user@example.com',
    'password' => bcrypt('password'),
    'role' => 'user',
]);

// Exit tinker
exit
```

**Important:** Always use `bcrypt()` or `Hash::make()` for passwords!

### Using a Seeder (Alternative)

Create a seeder:

```bash
docker exec laravel12_app php artisan make:seeder UserRoleSeeder
```

Edit `database/seeders/UserRoleSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        // Create manager
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MANAGER,
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
        ]);
    }
}
```

Run the seeder:

```bash
docker exec laravel12_app php artisan db:seed --class=UserRoleSeeder
```

---

## Part 4: Create Authentication Controller

### Step 1: Create the Controller

```bash
docker exec laravel12_app php artisan make:controller AuthController
```

### Step 2: Add Authentication Methods

Edit `app/Http/Controllers/AuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate the input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to log the user in
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            // Redirect to dashboard with success message
            return redirect()
                ->intended('dashboard')
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        // If login fails, redirect back with error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $name = Auth::user()->name;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('info', 'Goodbye, ' . $name . '! You have been logged out.');
    }
}
```

**Understanding the Code:**

- **`validate()`** - Ensures email and password are provided
- **`Auth::attempt()`** - Tries to authenticate with credentials
- **`$request->filled('remember')`** - Checks if "remember me" checkbox is checked
- **`session()->regenerate()`** - Security: prevents session fixation
- **`redirect()->intended()`** - Goes to originally requested page or fallback
- **`withErrors()`** - Flashes validation errors to session
- **`onlyInput('email')`** - Only keeps email in old input (not password)

---

## Part 5: Create Dashboard Controllers

### Step 1: Create Dashboard Controllers for Each Role

```bash
docker exec laravel12_app php artisan make:controller DashboardController
docker exec laravel12_app php artisan make:controller Admin/AdminDashboardController
docker exec laravel12_app php artisan make:controller Manager/ManagerDashboardController
```

### Step 2: Edit DashboardController (General)

Edit `app/Http/Controllers/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        // Redirect to role-specific dashboard
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            'user' => view('dashboard.user'),
            default => view('dashboard.user'),
        };
    }
}
```

**Understanding `match()`:**

- Modern PHP 8 syntax (like switch but cleaner)
- Checks the user's role
- Redirects to appropriate dashboard
- Has a default fallback

### Step 3: Edit Admin Dashboard Controller

Edit `app/Http/Controllers/Admin/AdminDashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        // Only admins can access
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $managerCount = User::where('role', 'manager')->count();
        $userCount = User::where('role', 'user')->count();

        return view('dashboard.admin', compact(
            'totalUsers',
            'adminCount',
            'managerCount',
            'userCount'
        ));
    }
}
```

### Step 4: Edit Manager Dashboard Controller

Edit `app/Http/Controllers/Manager/ManagerDashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    public function __construct()
    {
        // Only managers and admins can access
        $this->middleware(['auth', 'role:admin,manager']);
    }

    public function index()
    {
        $regularUsers = User::where('role', 'user')->get();

        return view('dashboard.manager', compact('regularUsers'));
    }
}
```

---

## Part 6: Create Role Middleware

### Step 1: Create the Middleware

```bash
docker exec laravel12_app php artisan make:middleware CheckRole
```

### Step 2: Edit the Middleware

Edit `app/Http/Middleware/CheckRole.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has one of the allowed roles
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```

**Understanding the Code:**

- `string ...$roles` - Accepts multiple roles (e.g., 'admin,manager')
- `!$request->user()` - Checks if user is logged in
- `in_array()` - Checks if user's role is in allowed roles
- `abort(403)` - Returns 403 Forbidden error if unauthorized

### Step 3: Register the Middleware

Edit `app/Http/Kernel.php` or `bootstrap/app.php` (Laravel 11+):

**For Laravel 11+**, edit `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

**For Laravel 10 and below**, edit `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ... existing middleware
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

**What this does:** Registers the middleware so you can use `middleware('role:admin')` in routes.

---

## Part 7: Create Login View

### Step 1: Create Auth Directory

```bash
mkdir -p resources/views/auth
```

### Step 2: Create Login Blade Template

Create `resources/views/auth/login.blade.php`:

```blade
@extends('layouts.app', ['hideNavigation' => true, 'hideFooter' => true])

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        {{-- Logo/Title --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">{{ config('app.name', 'Laravel') }}</h1>
            <p class="text-gray-600 mt-2">Sign in to your account</p>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email Field --}}
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                    required
                    autofocus
                    placeholder="Enter your email"
                >
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Field --}}
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                    required
                    placeholder="Enter your password"
                >
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>

                {{-- Uncomment if you add password reset:
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Forgot password?
                </a>
                --}}
            </div>

            {{-- Submit Button --}}
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg"
            >
                Sign In
            </button>
        </form>

        {{-- Test Credentials Info (Remove in production!) --}}
        <div class="mt-6 p-4 bg-gray-100 rounded-lg">
            <p class="text-xs text-gray-700 font-semibold mb-2">Test Credentials:</p>
            <p class="text-xs text-gray-600">Admin: admin@example.com</p>
            <p class="text-xs text-gray-600">Manager: manager@example.com</p>
            <p class="text-xs text-gray-600">User: user@example.com</p>
            <p class="text-xs text-gray-600">Password: password</p>
        </div>
    </div>
</div>
@endsection
```

**Key Features:**

- Clean, modern design
- Error handling with visual feedback
- Remember me functionality
- Responsive (works on mobile)
- Test credentials shown for development

---

## Part 8: Create Dashboard Views

### Step 1: Create Dashboard Directory

```bash
mkdir -p resources/views/dashboard
```

### Step 2: Create Admin Dashboard

Create `resources/views/dashboard/admin.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
            ADMIN
        </span>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Admins</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $adminCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Managers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $managerCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Regular Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $userCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin Controls --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Admin Controls</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="#" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <div class="ml-4">
                    <p class="font-semibold text-gray-900">Manage Users</p>
                    <p class="text-sm text-gray-600">Add, edit, delete users</p>
                </div>
            </a>

            <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div class="ml-4">
                    <p class="font-semibold text-gray-900">Settings</p>
                    <p class="text-sm text-gray-600">System configuration</p>
                </div>
            </a>

            <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div class="ml-4">
                    <p class="font-semibold text-gray-900">Reports</p>
                    <p class="text-sm text-gray-600">View analytics</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
```

### Step 3: Create Manager Dashboard

Create `resources/views/dashboard/manager.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Manager Dashboard</h1>
        <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
            MANAGER
        </span>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Welcome Message --}}
    <div class="bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg p-6 mb-8">
        <h2 class="text-2xl font-bold mb-2">Welcome, {{ Auth::user()->name }}!</h2>
        <p>You have manager-level access to this system.</p>
    </div>

    {{-- Users List --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Regular Users</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($regularUsers as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                No regular users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```

### Step 4: Create User Dashboard

Create `resources/views/dashboard/user.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">My Dashboard</h1>
        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
            USER
        </span>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg p-8 mb-8">
        <h2 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h2>
        <p class="text-blue-100">You're logged in as a regular user.</p>
    </div>

    {{-- User Info --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Your Account Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Name</p>
                <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="font-semibold text-gray-900">{{ Auth::user()->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Role</p>
                <p class="font-semibold text-gray-900 capitalize">{{ Auth::user()->role }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Member Since</p>
                <p class="font-semibold text-gray-900">{{ Auth::user()->created_at->format('F d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-blue-500 mb-4 flex justify-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h4 class="font-bold text-gray-900 mb-2">Profile</h4>
            <p class="text-sm text-gray-600 mb-4">Update your information</p>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">View Profile →</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-green-500 mb-4 flex justify-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h4 class="font-bold text-gray-900 mb-2">Documents</h4>
            <p class="text-sm text-gray-600 mb-4">Access your documents</p>
            <a href="#" class="text-green-600 hover:text-green-800 text-sm font-semibold">Browse →</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition">
            <div class="text-purple-500 mb-4 flex justify-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h4 class="font-bold text-gray-900 mb-2">Settings</h4>
            <p class="text-sm text-gray-600 mb-4">Manage preferences</p>
            <a href="#" class="text-purple-600 hover:text-purple-800 text-sm font-semibold">Configure →</a>
        </div>
    </div>
</div>
@endsection
```

---

## Part 9: Define Routes

### Update routes/web.php

Edit `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Manager\ManagerDashboardController;

// Redirect root to home page
Route::get('/', function () {
    return view('home');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// General Dashboard (redirects based on role)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Admin Dashboard
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard')
    ->middleware(['auth', 'role:admin']);

// Manager Dashboard
Route::get('/manager/dashboard', [ManagerDashboardController::class, 'index'])
    ->name('manager.dashboard')
    ->middleware(['auth', 'role:admin,manager']);

// Test routes
Route::get('/test', function () {
    return view('test-tailwind');
});

Route::get('/test-simple', function () {
    return view('test-simple');
});
```

**Understanding the Routes:**

- **`middleware('guest')`** - Only accessible to non-logged-in users
- **`middleware('auth')`** - Only accessible to logged-in users
- **`middleware(['auth', 'role:admin'])`** - Only admins
- **`middleware(['auth', 'role:admin,manager'])`** - Admins OR managers

---

## Part 10: Update Navigation

Now that routes exist, update the navigation to uncomment the links.

### Edit resources/views/layouts/partials/navigation.blade.php:

Uncomment the dashboard link (around line 17-21):

```blade
<a href="{{ route('dashboard') }}"
   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium transition">
    Dashboard
</a>
```

Uncomment the login link (around line 95-99):

```blade
<a href="{{ route('login') }}"
   class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition">
    Login
</a>
```

Uncomment the logout form in the dropdown (around line 86-94):

```blade
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
        Logout
    </button>
</form>
```

---

## Part 11: Testing the System

### Step 1: Create Test Users

If you haven't already, create test users using Tinker (see Part 3).

### Step 2: Test Login Flow

1. **Visit** http://localhost:8000/login
2. **Try logging in as admin:**
   - Email: `admin@example.com`
   - Password: `password`
3. **You should see:** Admin dashboard with stats
4. **Logout** and try other roles

### Step 3: Test Role Protection

1. **Login as regular user** (`user@example.com`)
2. **Try to access** http://localhost:8000/admin/dashboard
3. **You should see:** 403 Forbidden error
4. **This is correct!** Users can't access admin pages

---

## Part 12: Using Roles in Blade Templates

### Show Content Based on Role

```blade
@if(Auth::user()->isAdmin())
    <p>This is only visible to admins!</p>
@endif

@if(Auth::user()->isManager())
    <p>This is only visible to managers!</p>
@endif

@if(Auth::user()->hasAnyRole(['admin', 'manager']))
    <p>This is visible to admins and managers!</p>
@endif
```

### Alternative Using @can Directive

Define gates in `app/Providers/AuthServiceProvider.php`:

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('manage-users', function ($user) {
        return $user->isAdmin();
    });

    Gate::define('view-reports', function ($user) {
        return $user->hasAnyRole(['admin', 'manager']);
    });
}
```

Then in Blade:

```blade
@can('manage-users')
    <a href="{{ route('users.index') }}">Manage Users</a>
@endcan

@can('view-reports')
    <a href="{{ route('reports') }}">View Reports</a>
@endcan
```

---

## Part 13: Protecting Controller Methods

### Using Middleware in Constructor

```php
public function __construct()
{
    $this->middleware('role:admin');
}
```

### Using Middleware in Routes

```php
Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware(['auth', 'role:admin']);
```

### Checking in Controller Method

```php
public function destroy(User $user)
{
    // Only admins can delete users
    if (!Auth::user()->isAdmin()) {
        abort(403, 'Only administrators can delete users.');
    }

    $user->delete();

    return redirect()->back()->with('success', 'User deleted successfully.');
}
```

---

## 📊 Role Hierarchy Summary

| Role        | Access Level | Can Access                |
| ----------- | ------------ | ------------------------- |
| **Admin**   | Full         | Everything                |
| **Manager** | Medium       | Manager + User dashboards |
| **User**    | Basic        | User dashboard only       |

---

## 🎯 Quick Reference Commands

```bash
# Create migration
docker exec laravel12_app php artisan make:migration add_role_to_users_table

# Run migration
docker exec laravel12_app php artisan migrate

# Create controller
docker exec laravel12_app php artisan make:controller AuthController

# Create middleware
docker exec laravel12_app php artisan make:middleware CheckRole

# Create seeder
docker exec laravel12_app php artisan make:seeder UserRoleSeeder

# Run seeder
docker exec laravel12_app php artisan db:seed --class=UserRoleSeeder

# Open Tinker
docker exec laravel12_app php artisan tinker
```

---

## 🔐 Security Best Practices

1. **Always validate input** before authentication
2. **Regenerate session** after login
3. **Invalidate session** after logout
4. **Use CSRF protection** on all forms (`@csrf`)
5. **Hash passwords** with `bcrypt()` or `Hash::make()`
6. **Check roles** in both routes AND controllers
7. **Never trust client-side validation** alone

---

## 🚀 Next Steps

### Enhance Your System:

1. **Add Registration** - Allow users to sign up
2. **Password Reset** - Forgot password functionality
3. **Email Verification** - Verify email addresses
4. **Profile Management** - Let users update their info
5. **Activity Logs** - Track user actions
6. **Role Management UI** - Let admins change user roles
7. **Permissions** - More granular than roles

---

## 🐛 Troubleshooting

### "Route [login] not defined"

Make sure you've added login routes to `routes/web.php`.

### 403 Forbidden Error

This is correct! It means role protection is working. User doesn't have permission.

### Can't login

1. Check users exist in database
2. Verify password is hashed with `bcrypt()`
3. Check `.env` has correct DB credentials
4. Check session driver is set (use 'file' for development)

### Role middleware not working

1. Verify middleware is registered in `bootstrap/app.php` or `Kernel.php`
2. Check middleware alias is 'role'
3. Ensure migration has run (role column exists)

---

**Congratulations!** You now have a complete role-based authentication system! 🎉

Test it thoroughly with all three roles to understand how the access control works.

