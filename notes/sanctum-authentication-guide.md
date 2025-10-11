# Laravel Sanctum Authentication Guide

## Overview

Laravel Sanctum provides authentication for SPAs (Single Page Applications) and mobile applications. It offers two types of authentication:

1. **SPA Authentication** - Cookie-based authentication for SPAs (same domain)
2. **API Token Authentication** - Token-based authentication for mobile apps or third-party APIs

---

## Sanctum Setup for Your Project

### 1. Verify Sanctum Installation

Your project already has Sanctum installed! You can see `HasApiTokens` trait in your User model.

Check that the `personal_access_tokens` table migration exists:

```bash
ls database/migrations/*personal_access_tokens*
```

If migrations haven't been run yet:

```bash
php artisan migrate
```

---

## Option A: SPA Authentication (Single Page Apps)

Use this if you're building a Vue/React/Inertia.js frontend on the same domain.

### Configuration Steps:

#### 1. Configure CORS (if frontend is on different subdomain)

In `config/cors.php`, ensure:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register'],
'supports_credentials' => true,
```

#### 2. Configure Sanctum

Publish Sanctum config if not already done:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

In `config/sanctum.php`, set your frontend URL:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),
```

#### 3. Add Sanctum middleware

In `bootstrap/app.php` (Laravel 11) or `app/Http/Kernel.php` (Laravel 10):

**Laravel 11:**

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
})
```

**Laravel 10:**

```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

#### 4. Create Authentication Controllers

**Register Controller:**

```bash
php artisan make:controller Auth/RegisterController
```

```php
public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    // Optionally assign a default role
    $user->assignRole(Role::where('name', 'user')->first());

    Auth::login($user);

    return response()->json([
        'user' => $user,
        'message' => 'Registration successful'
    ], 201);
}
```

**Login Controller:**

```bash
php artisan make:controller Auth/LoginController
```

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $request->session()->regenerate();

    return response()->json([
        'user' => Auth::user(),
        'message' => 'Login successful'
    ]);
}

public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Logout successful'
    ]);
}
```

**Get Current User:**

```php
public function user(Request $request)
{
    return response()->json([
        'user' => $request->user()->load('roles')
    ]);
}
```

#### 5. Create Routes

In `routes/api.php`:

```php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Public routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', [LoginController::class, 'user']);
});
```

#### 6. Frontend Setup

Before making authenticated requests, your SPA must request a CSRF cookie:

```javascript
// Get CSRF cookie first
await axios.get("/sanctum/csrf-cookie");

// Then login
await axios.post("/api/login", {
  email: "user@example.com",
  password: "password",
});

// Make authenticated requests
await axios.get("/api/user");
```

---

## Option B: API Token Authentication (Mobile Apps/APIs)

Use this for mobile apps or when frontend is on a completely different domain.

### Implementation Steps:

#### 1. Create Authentication Controllers

**Login Controller:**

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $user = Auth::user();

    // Create token with abilities (optional)
    $token = $user->createToken('auth-token', ['*'])->plainTextToken;

    return response()->json([
        'user' => $user->load('roles'),
        'token' => $token,
        'token_type' => 'Bearer'
    ]);
}
```

**Logout Controller:**

```php
public function logout(Request $request)
{
    // Revoke current token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logout successful'
    ]);
}

public function logoutAll(Request $request)
{
    // Revoke all tokens
    $request->user()->tokens()->delete();

    return response()->json([
        'message' => 'Logged out from all devices'
    ]);
}
```

#### 2. Create Routes

In `routes/api.php`:

```php
// Public routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/logout-all', [LoginController::class, 'logoutAll']);
});
```

#### 3. Client Usage

Include the token in the Authorization header:

```javascript
// Login and get token
const response = await fetch("/api/login", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    email: "user@example.com",
    password: "password",
  }),
});

const { token } = await response.json();

// Make authenticated requests
const userResponse = await fetch("/api/user", {
  headers: {
    Authorization: `Bearer ${token}`,
    "Content-Type": "application/json",
  },
});
```

---

## Adding Role-Based Protection to API Routes

### Method 1: Using Middleware

Create a custom middleware for role checking:

```bash
php artisan make:middleware CheckRole
```

```php
public function handle(Request $request, Closure $next, ...$roles)
{
    if (!$request->user()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    foreach ($roles as $role) {
        if ($request->user()->hasRole($role)) {
            return $next($request);
        }
    }

    return response()->json(['message' => 'Forbidden'], 403);
}
```

Register middleware and use in routes:

```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index']);
});
```

### Method 2: Using Sanctum Abilities

When creating tokens, assign specific abilities:

```php
// Login controller
$token = $user->createToken('auth-token', [
    'user:read',
    'user:update',
    'posts:create',
])->plainTextToken;
```

Check abilities in routes:

```php
Route::middleware(['auth:sanctum', 'abilities:posts:create'])->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
});

// Or check multiple abilities
Route::middleware(['auth:sanctum', 'abilities:posts:create,posts:update'])->group(function () {
    // Route handlers
});
```

Check abilities in controller:

```php
if ($request->user()->tokenCan('posts:create')) {
    // User can create posts
}
```

---

## Environment Configuration

Add to your `.env`:

```env
# For SPA authentication
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8080,yourfrontend.com

# Session configuration (important for SPA auth)
SESSION_DRIVER=cookie
SESSION_DOMAIN=.yourdomain.com
```

---

## Testing Your Sanctum Authentication

### Test with Postman/Insomnia:

**1. Register/Login:**

```
POST http://localhost:8000/api/login
Body: { "email": "user@test.com", "password": "password" }
```

**2. Copy the returned token**

**3. Make authenticated request:**

```
GET http://localhost:8000/api/user
Header: Authorization: Bearer YOUR_TOKEN_HERE
```

### Test with Artisan Tinker:

```bash
php artisan tinker
```

```php
// Create a user
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
]);

// Assign role
$user->assignRole(Role::where('name', 'user')->first());

// Create a token
$token = $user->createToken('test-token')->plainTextToken;

// Display token
echo $token;
```

---

## Security Best Practices

1. **Always use HTTPS in production**
2. **Set token expiration:**

   ```php
   // In config/sanctum.php
   'expiration' => 60, // minutes (null = never expire)
   ```

3. **Rate limiting:**

   ```php
   Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
       // Max 60 requests per minute
   });
   ```

4. **Validate all inputs**
5. **Use password hashing** (already done with 'hashed' cast)
6. **Implement CSRF protection** for SPA authentication

---

## Common Issues & Solutions

### Issue: "Unauthenticated" even with valid token

**Solution:**

- Ensure `Authorization: Bearer TOKEN` header is set correctly
- Check that `auth:sanctum` middleware is applied
- Verify token exists in `personal_access_tokens` table

### Issue: CORS errors in SPA

**Solution:**

- Configure `SANCTUM_STATEFUL_DOMAINS` in `.env`
- Ensure `supports_credentials: true` in `config/cors.php`
- Frontend must include `withCredentials: true` in requests

### Issue: Session not persisting

**Solution:**

- Check `SESSION_DRIVER` in `.env`
- Ensure cookies are enabled
- For localhost development, don't use `.localhost` domain

---

## Quick Commands Reference

```bash
# Publish Sanctum config
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run migrations (includes personal_access_tokens)
php artisan migrate

# Create controllers
php artisan make:controller Auth/LoginController
php artisan make:controller Auth/RegisterController

# Create middleware
php artisan make:middleware CheckRole

# Clear config cache
php artisan config:clear

# Test in Tinker
php artisan tinker
```

---

## Next Steps

1. Decide: SPA auth or API token auth?
2. Create authentication controllers
3. Set up routes
4. Test authentication flow
5. Add role-based protection to routes
6. Implement frontend authentication
7. Add password reset functionality (if needed)

---

**Last Updated:** October 10, 2025

