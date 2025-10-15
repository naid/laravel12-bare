# Tailwind CSS Setup Guide for Laravel

⚠️ **NOTE:** This project now uses **Tailwind CSS v3** for better stability and compatibility.

For the working v3 setup, see the configuration files in the project root. For troubleshooting, see `md/TROUBLESHOOTING_TAILWIND.md`.

---

## Original Tailwind v4 Guide (For Reference)

The following was the original v4 guide. We've since switched to v3 for better reliability.

## Step 1: Update Your CSS File

Create or update `resources/css/app.css` with Tailwind v4 directives:

```css
@import "tailwindcss";
```

That's it! Just one line. Tailwind v4 automatically handles everything.

### Optional: Add Custom Configuration

If you want to customize your theme, you can add a `@theme` directive:

```css
@import "tailwindcss";

@theme {
  --color-primary: #3b82f6;
  --color-secondary: #8b5cf6;

  --font-sans: "Inter", system-ui, sans-serif;
}
```

## Step 2: Create Vite Config

Create `vite.config.js` in your project root:

```javascript
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
  ],
});
```

## Step 3: Create JavaScript File

Create `resources/js/app.js` (can be empty or add your JS):

```javascript
// Your JavaScript code here
console.log("Laravel + Vite + Tailwind v4!");
```

## Step 4: Install Dependencies (Docker)

Since you're using Docker, run these commands:

```bash
# Install all dependencies
docker exec -it laravel12_app npm install

# That's it! No npx tailwindcss init needed for v4
```

## Step 5: Create Layout Template

Create `resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    @yield('content')
</body>
</html>
```

## Step 6: Start Vite Dev Server (Docker)

```bash
# Start the dev server inside Docker container
docker exec -it laravel12_app npm run dev
```

Keep this running while you develop!

## Step 7: Test It!

Create a test view `resources/views/test.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Tailwind v4 Test')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md">
        <h1 class="text-3xl font-bold text-blue-600 mb-4">
            🎉 Tailwind CSS v4 is Working!
        </h1>
        <p class="text-gray-700 mb-4">
            This is styled with Tailwind v4. No config files needed!
        </p>
        <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-200">
            Click Me
        </button>
    </div>
</div>
@endsection
```

Add a route in `routes/web.php`:

```php
Route::get('/test', function () {
    return view('test');
});
```

Visit `http://localhost:8000/test` to see it in action!

---

## What's Different in Tailwind v4?

### ✅ No More Config Files

- No `tailwind.config.js`
- No `postcss.config.js`
- Configuration is done in CSS with `@theme`

### ✅ Simpler CSS Import

```css
/* v3 way (OLD) */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* v4 way (NEW) */
@import "tailwindcss";
```

### ✅ Faster Build Times

Tailwind v4 is built with Rust and is significantly faster than v3.

### ✅ New Features

- Native cascade layers
- Better CSS variable support
- Improved container queries
- Modern CSS features

---

## Authentication Setup with Tailwind v4

Now that Tailwind is set up, here's how to create a login page:

### Create Login View

Create `resources/views/auth/login.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    required
                    autofocus
                >
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm text-gray-700">Remember Me</span>
                </label>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200"
            >
                Login
            </button>
        </form>
    </div>
</div>
@endsection
```

### Create Dashboard View

Create `resources/views/dashboard.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">Laravel App</h1>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-200"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-4">Welcome to your Dashboard!</h2>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <p class="text-blue-700">
                        You are logged in as: <strong>{{ Auth::user()->email }}</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg text-white">
                        <h3 class="text-lg font-semibold mb-2">Total Users</h3>
                        <p class="text-3xl font-bold">{{ \App\Models\User::count() }}</p>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg text-white">
                        <h3 class="text-lg font-semibold mb-2">Your Account</h3>
                        <p class="text-sm">Created: {{ Auth::user()->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg text-white">
                        <h3 class="text-lg font-semibold mb-2">Status</h3>
                        <p class="text-sm">Active</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### Create Controllers

Create `app/Http/Controllers/AuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
```

Create `app/Http/Controllers/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard');
    }
}
```

### Define Routes

Update `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');
```

### Create Test User

Use Tinker to create a test user:

```bash
docker exec -it laravel12_app php artisan tinker
```

Then:

```php
\App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
]);
```

---

## Quick Start Commands (Docker)

```bash
# 1. Make sure containers are running
docker-compose up -d

# 2. Install dependencies (first time)
docker exec -it laravel12_app npm install

# 3. Start Vite dev server
docker exec -it laravel12_app npm run dev

# 4. In another terminal, create test user
docker exec -it laravel12_app php artisan tinker

# 5. Visit http://localhost:8000/login
```

---

## Troubleshooting

### Styles not loading?

1. Make sure `npm run dev` is running
2. Check that `@vite()` directive is in your layout
3. Clear browser cache
4. Check browser console for errors

### npm run dev fails?

```bash
# Rebuild Docker container
docker-compose down
docker-compose build --no-cache app
docker-compose up -d

# Try again
docker exec -it laravel12_app npm install
docker exec -it laravel12_app npm run dev
```

### Want to downgrade to Tailwind v3?

If you prefer v3 (with config files), you can downgrade:

```bash
docker exec -it laravel12_app npm install -D tailwindcss@^3
docker exec -it laravel12_app npx tailwindcss init -p
```

Then update your CSS to use the v3 directives.

---

## Summary

**Tailwind v4 is simpler:**

- ✅ No `tailwind.config.js`
- ✅ No `postcss.config.js`
- ✅ Just `@import "tailwindcss";` in your CSS
- ✅ Faster builds
- ✅ Same great utility classes

Happy coding! 🚀
