# Role-Based Authentication Implementation Checklist

Use this checklist to implement the role-based authentication system step by step.

## ✅ Checklist Overview

- [ ] Database: Add role column to users table
- [ ] Model: Update User model with role methods
- [ ] Middleware: Create role checking middleware
- [ ] Seeder: Create test users with roles
- [ ] Controllers: Create auth and dashboard controllers
- [ ] Views: Create login and dashboard views
- [ ] Routes: Define authentication and dashboard routes
- [ ] Navigation: Update navigation with auth links
- [ ] Testing: Test all three roles

---

## 📝 Step-by-Step Implementation

### ✅ Step 1: Run the Migration

The migration has already been created at:
`database/migrations/2025_10_15_171755_add_role_to_users_table.php`

**Run it:**

```bash
docker exec laravel12_app php artisan migrate
```

**Verify it worked:**

```bash
docker exec laravel12_app php artisan tinker
```

Then check the users table structure:

```php
\Schema::getColumnListing('users');
// Should include: id, name, email, email_verified_at, password, role, remember_token, created_at, updated_at
exit
```

---

### ✅ Step 2: Update User Model

**File:** `app/Models/User.php`

**Add role to $fillable:**

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',  // Add this
];
```

**Add these constants at the top of the class:**

```php
// Role constants
const ROLE_ADMIN = 'admin';
const ROLE_MANAGER = 'manager';
const ROLE_USER = 'user';
```

**Add these methods at the bottom of the class:**

```php
public function isAdmin(): bool
{
    return $this->role === self::ROLE_ADMIN;
}

public function isManager(): bool
{
    return $this->role === self::ROLE_MANAGER;
}

public function isUser(): bool
{
    return $this->role === self::ROLE_USER;
}

public function hasRole(string $role): bool
{
    return $this->role === $role;
}

public function hasAnyRole(array $roles): bool
{
    return in_array($this->role, $roles);
}
```

---

### ✅ Step 3: Create Role Middleware

**Create the middleware:**

```bash
docker exec laravel12_app php artisan make:middleware CheckRole
```

**Edit:** `app/Http/Middleware/CheckRole.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```

**Register it in** `bootstrap/app.php`:

Find the `withMiddleware` section and add:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

---

### ✅ Step 4: Create Test Users

**Option A: Using Tinker (Quick)**

```bash
docker exec laravel12_app php artisan tinker
```

```php
\App\Models\User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => 'admin']);

\App\Models\User::create(['name' => 'Manager User', 'email' => 'manager@example.com', 'password' => bcrypt('password'), 'role' => 'manager']);

\App\Models\User::create(['name' => 'Regular User', 'email' => 'user@example.com', 'password' => bcrypt('password'), 'role' => 'user']);

exit
```

**Option B: Using Seeder (Better for teams)**

See the full guide in `ROLE_BASED_AUTH_GUIDE.md`.

---

### ✅ Step 5: Create Controllers

**Create AuthController:**

```bash
docker exec laravel12_app php artisan make:controller AuthController
```

**Create DashboardController:**

```bash
docker exec laravel12_app php artisan make:controller DashboardController
```

**Create Admin Dashboard:**

```bash
docker exec laravel12_app php artisan make:controller Admin/AdminDashboardController
```

**Create Manager Dashboard:**

```bash
docker exec laravel12_app php artisan make:controller Manager/ManagerDashboardController
```

**Now edit each controller** with the code from `ROLE_BASED_AUTH_GUIDE.md` Part 4 and Part 5.

---

### ✅ Step 6: Create Views

**Create directories:**

```bash
mkdir -p resources/views/auth
mkdir -p resources/views/dashboard
```

**Create these files:**

1. `resources/views/auth/login.blade.php` - Login page
2. `resources/views/dashboard/admin.blade.php` - Admin dashboard
3. `resources/views/dashboard/manager.blade.php` - Manager dashboard
4. `resources/views/dashboard/user.blade.php` - User dashboard

**Copy the code** from `ROLE_BASED_AUTH_GUIDE.md` Part 7 and Part 8.

---

### ✅ Step 7: Define Routes

**Edit:** `routes/web.php`

Add the authentication and dashboard routes from `ROLE_BASED_AUTH_GUIDE.md` Part 9.

**Key routes to add:**

- `GET /login` - Show login form
- `POST /login` - Process login
- `POST /logout` - Logout user
- `GET /dashboard` - General dashboard (redirects by role)
- `GET /admin/dashboard` - Admin only
- `GET /manager/dashboard` - Manager and admin

---

### ✅ Step 8: Update Navigation

**Edit:** `resources/views/layouts/partials/navigation.blade.php`

**Uncomment these sections:**

1. Dashboard link (line ~17-21)
2. Login link (line ~95-99)
3. Logout button in dropdown (line ~86-94)

See `ROLE_BASED_AUTH_GUIDE.md` Part 10 for exact code.

---

### ✅ Step 9: Rebuild CSS

After creating all views:

```bash
docker exec laravel12_app npm run build
```

---

### ✅ Step 10: Test the System

**Test Each Role:**

1. **Admin** (`admin@example.com` / `password`)

   - Should see admin dashboard with stats
   - Can access all dashboards

2. **Manager** (`manager@example.com` / `password`)

   - Should see manager dashboard with user list
   - Can access manager and user dashboards
   - CANNOT access admin dashboard

3. **User** (`user@example.com` / `password`)
   - Should see user dashboard with profile info
   - CANNOT access admin or manager dashboards

**Test URLs:**

- http://localhost:8000/login
- http://localhost:8000/dashboard
- http://localhost:8000/admin/dashboard
- http://localhost:8000/manager/dashboard

---

## 📊 Files You'll Create/Edit

### Create These Files:

- [ ] `app/Http/Middleware/CheckRole.php`
- [ ] `app/Http/Controllers/AuthController.php`
- [ ] `app/Http/Controllers/DashboardController.php`
- [ ] `app/Http/Controllers/Admin/AdminDashboardController.php`
- [ ] `app/Http/Controllers/Manager/ManagerDashboardController.php`
- [ ] `resources/views/auth/login.blade.php`
- [ ] `resources/views/dashboard/admin.blade.php`
- [ ] `resources/views/dashboard/manager.blade.php`
- [ ] `resources/views/dashboard/user.blade.php`

### Edit These Files:

- [ ] `app/Models/User.php` - Add role constants and methods
- [ ] `bootstrap/app.php` - Register role middleware
- [ ] `routes/web.php` - Add authentication routes
- [ ] `resources/views/layouts/partials/navigation.blade.php` - Uncomment auth links

### Migration Already Created:

- [x] `database/migrations/2025_10_15_171755_add_role_to_users_table.php` ✅

---

## 🎓 Learning Outcomes

By implementing this system, you'll learn:

1. **Database Design** - How to add columns with constraints (enum)
2. **Migrations** - How to modify existing tables
3. **Models** - Adding methods and constants for cleaner code
4. **Middleware** - Protecting routes based on conditions
5. **Authentication** - How Laravel's Auth system works
6. **Authorization** - Role-based access control
7. **Controllers** - Separating logic by responsibility
8. **Blade Templates** - Conditional rendering based on roles
9. **Routes** - Route protection with middleware
10. **Sessions** - How login sessions work

---

## 💡 Pro Tips

1. **Start with the migration** - Database first
2. **Test after each step** - Don't build everything at once
3. **Use Tinker often** - Great for testing database queries
4. **Read error messages** - Laravel gives helpful errors
5. **Check the guide** - `ROLE_BASED_AUTH_GUIDE.md` has all the code

---

## 🚀 Quick Start (If You Want to Code Yourself)

Follow this order:

1. Run migration → Test in Tinker
2. Update User model → Test methods in Tinker
3. Create middleware → Register it
4. Create test users → Verify in DB
5. Create AuthController → Test login logic
6. Create login view → Test login page appears
7. Add routes → Test login works
8. Create dashboards → Test role redirects
9. Update navigation → Test logout

---

## 🆘 Need Help?

- **Full code examples**: See `ROLE_BASED_AUTH_GUIDE.md`
- **Concept explanations**: Read the guide step by step
- **Troubleshooting**: Check the troubleshooting section in the guide

---

**Ready to implement? Follow the checklist above and refer to the guide for code examples!** 🚀

