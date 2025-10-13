# Quick Security Setup Guide

## Step-by-Step Setup

### 1. Run Migrations

```bash
docker-compose exec app php artisan migrate
```

This adds:

- `role` field to users table (admin, manager, user)
- `client_user` pivot table for access control

### 2. Run Seeder (Optional - Creates Test Users)

```bash
docker-compose exec app php artisan db:seed --class=UserClientSeeder
```

Creates test users:

- **admin@example.com** - Can access all clients
- **manager@example.com** - Can access assigned clients (with write)
- **user@example.com** - Can access assigned clients (read-only)
- All passwords: **password**

### 3. Update Existing Users

If you have existing users, set their roles:

```sql
-- Set a user as admin
UPDATE users SET role = 'admin' WHERE email = 'youradmin@example.com';

-- Set a user as manager
UPDATE users SET role = 'manager' WHERE email = 'yourmanager@example.com';

-- Regular users default to 'user' role
```

### 4. Assign Users to Clients

#### Option A: Via Database

```sql
-- Give user ID 2 read access to client ID 1
INSERT INTO client_user (client_id, user_id, access_level, created_at, updated_at)
VALUES (1, 2, 'read', NOW(), NOW());

-- Give user ID 3 write access to client ID 1
INSERT INTO client_user (client_id, user_id, access_level, created_at, updated_at)
VALUES (1, 3, 'write', NOW(), NOW());
```

#### Option B: Via Tinker

```bash
docker-compose exec app php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'user@example.com')->first();
$client = \App\Models\Client::find(1);

// Attach with access level
$user->clients()->attach($client->id, ['access_level' => 'read']);

// Or sync multiple
$user->clients()->sync([
    1 => ['access_level' => 'read'],
    2 => ['access_level' => 'write'],
]);
```

#### Option C: In Code (e.g., in a seeder or controller)

```php
use App\Models\User;
use App\Models\Client;

$user = User::find($userId);
$user->clients()->attach($clientId, ['access_level' => 'read']);
```

### 5. Test the Security

1. **Login as admin** (admin@example.com)

   - Should see ALL clients in the list
   - Can select any client

2. **Login as regular user** (user@example.com)

   - Should only see assigned clients
   - Can only select assigned clients
   - Trying to select unauthorized client = 403 error

3. **Remove access while client is selected**
   - Select a client
   - Remove user's access (via database)
   - Refresh page
   - Client should auto-clear with warning message

## Quick Reference

### User Roles

| Role        | Can View All Clients  | Can Create Clients | Can Update Clients         | Can Delete Clients |
| ----------- | --------------------- | ------------------ | -------------------------- | ------------------ |
| **admin**   | ✅ Yes                | ✅ Yes             | ✅ Yes                     | ✅ Yes             |
| **manager** | ❌ No (assigned only) | ✅ Yes             | ✅ Yes (with write access) | ❌ No              |
| **user**    | ❌ No (assigned only) | ❌ No              | ❌ No                      | ❌ No              |

### Access Levels (in client_user pivot)

| Level     | Can View | Can Update | Can Manage |
| --------- | -------- | ---------- | ---------- |
| **read**  | ✅ Yes   | ❌ No      | ❌ No      |
| **write** | ✅ Yes   | ✅ Yes     | ❌ No      |
| **admin** | ✅ Yes   | ✅ Yes     | ✅ Yes     |

### Common Commands

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Seed test users
docker-compose exec app php artisan db:seed --class=UserClientSeeder

# Clear sessions (force re-validation)
docker-compose exec app php artisan session:clear

# Open tinker (for manual assignments)
docker-compose exec app php artisan tinker

# Check routes
docker-compose exec app php artisan route:list | grep client
```

## Assigning ALL Users to ALL Clients (Development Only)

If you want all existing users to access all clients (development/testing):

```bash
docker-compose exec app php artisan tinker
```

```php
$users = \App\Models\User::all();
$clients = \App\Models\Client::all();

foreach ($users as $user) {
    foreach ($clients as $client) {
        $user->clients()->syncWithoutDetaching([
            $client->id => ['access_level' => 'read']
        ]);
    }
}

echo "All users now have access to all clients!";
```

## Making a User an Admin

```bash
docker-compose exec app php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'your@email.com')->first();
$user->role = 'admin';
$user->save();

echo "User is now an admin!";
```

## Security Checklist

- [ ] Migrations run successfully
- [ ] User roles are assigned correctly
- [ ] Users are assigned to appropriate clients
- [ ] Test: Admin can see all clients
- [ ] Test: Regular user sees only assigned clients
- [ ] Test: Unauthorized client selection returns 403
- [ ] Test: Selected client auto-clears when access revoked
- [ ] Middleware is active (ValidateSelectedClient)
- [ ] ClientPolicy is enforced

## Troubleshooting

**Issue**: 403 error when selecting client

- **Solution**: Check `client_user` table, ensure user has access

**Issue**: Can't see any clients

- **Solution**: Either set role to 'admin' OR assign clients in `client_user` table

**Issue**: Selected client keeps clearing

- **Solution**: Check middleware logs, verify user still has access

**Issue**: Everyone can see all clients

- **Solution**: Verify migrations ran, check ClientController uses authorization

## Next Steps

After setup is complete:

1. Read `SECURITY_DOCUMENTATION.md` for full details
2. Read `GLOBAL_CLIENT_USAGE.md` for usage examples
3. Implement logging for audit trails (optional)
4. Add UI for admin to manage user-client assignments (optional)

---

## Production Deployment

Before deploying to production:

1. ✅ Run all migrations
2. ✅ Set all user roles appropriately
3. ✅ Assign all necessary client access
4. ✅ Test thoroughly with different user roles
5. ✅ Clear all development sessions
6. ✅ Enable proper error logging
7. ✅ Consider adding rate limiting on client selection endpoint
