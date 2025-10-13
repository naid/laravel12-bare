# 🔒 Security Quick Reference Card

## 🚀 Setup (Run Once)

```bash
# 1. Run migrations
docker-compose exec app php artisan migrate

# 2. Seed test users (optional)
docker-compose exec app php artisan db:seed --class=UserClientSeeder
```

## 👥 User Roles

| Role        | Access           | Permissions                            |
| ----------- | ---------------- | -------------------------------------- |
| **admin**   | All clients      | Full control (CRUD, select any)        |
| **manager** | Assigned clients | Can create, update (with write access) |
| **user**    | Assigned clients | Read-only                              |

## 🔑 Access Levels (client_user table)

| Level     | View | Update | Manage |
| --------- | ---- | ------ | ------ |
| **read**  | ✅   | ❌     | ❌     |
| **write** | ✅   | ✅     | ❌     |
| **admin** | ✅   | ✅     | ✅     |

## 📝 Quick Actions

### Set User Role

```bash
docker-compose exec app php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'user@example.com')->first();
$user->role = 'admin'; // or 'manager' or 'user'
$user->save();
```

### Assign Client Access

```php
$user = \App\Models\User::find($userId);
$user->clients()->attach($clientId, ['access_level' => 'read']); // or 'write' or 'admin'
```

### Remove Client Access

```php
$user->clients()->detach($clientId);
```

### Check User Permissions (in Controller)

```php
// Check if user can access client
if (auth()->user()->canAccessClient($client)) {
    // Allowed
}

// Or use authorization
$this->authorize('select', $client);
```

### Check in Blade Templates

```blade
@can('select', $client)
    <button>Select Client</button>
@else
    <span>No Access</span>
@endcan
```

## 🛡️ Security Features

✅ **Authorization Policy** - Controls who can view/select clients  
✅ **Middleware Validation** - Validates selected client on every request  
✅ **Role-Based Access** - Admin, manager, user roles  
✅ **Granular Permissions** - Read, write, admin access levels  
✅ **Auto-Cleanup** - Invalid selections cleared automatically  
✅ **403 Protection** - Unauthorized actions blocked

## 🧪 Test Users (if seeded)

| Email               | Password | Role    | Access              |
| ------------------- | -------- | ------- | ------------------- |
| admin@example.com   | password | admin   | All clients         |
| manager@example.com | password | manager | Clients 1-2 (write) |
| user@example.com    | password | user    | Client 1 (read)     |

## 📊 Database Structure

```sql
-- Users table (added field)
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user';

-- Client-User pivot table (new)
CREATE TABLE client_user (
    client_id BIGINT,
    user_id BIGINT,
    access_level VARCHAR(20) DEFAULT 'read'
);
```

## 🔍 How It Works

1. **User logs in** → Authenticated
2. **Clicks "Select Client"** → POST to `/clients/{id}/select`
3. **Authorization check** → `ClientPolicy::select()` validates
4. **If authorized** → Client stored in session
5. **On every request** → `ValidateSelectedClient` middleware validates
6. **If invalid** → Auto-cleared with warning

## 🚨 Common Issues

| Issue                     | Solution                                |
| ------------------------- | --------------------------------------- |
| 403 when selecting client | Add user to `client_user` table         |
| Can't see any clients     | Set role to 'admin' OR assign clients   |
| Selection keeps clearing  | User lost access, check permissions     |
| All users see all clients | Verify migrations ran, check controller |

## 📚 Documentation Files

- **SECURITY_DOCUMENTATION.md** - Complete security guide
- **SECURITY_SETUP.md** - Step-by-step setup instructions
- **GLOBAL_CLIENT_USAGE.md** - How to use global client feature
- **SECURITY_QUICK_REFERENCE.md** - This file

## 🎯 Key Code Locations

```
app/
├── Models/
│   ├── User.php              # Role methods, client relationship
│   └── Client.php            # User relationship
├── Policies/
│   └── ClientPolicy.php      # Authorization rules
├── Http/
│   ├── Controllers/
│   │   └── ClientController.php  # Authorization enforcement
│   └── Middleware/
│       └── ValidateSelectedClient.php  # Session validation
└── Helpers/
    └── ClientHelper.php      # Helper methods

database/
└── migrations/
    ├── *_add_role_to_users_table.php
    └── *_create_client_user_table.php

bootstrap/
└── app.php                   # Middleware registration
```

## 💡 Pro Tips

1. **Admins bypass all restrictions** - Be careful who you make admin
2. **Middleware validates on EVERY request** - Old sessions auto-cleaned
3. **Use helper functions** - `hasSelectedClient()`, `selectedClient()`
4. **Log everything** - Add logging to track who accesses what
5. **Test with different roles** - Ensure security works as expected

---

**Bottom Line**: Users can ONLY select and access clients they have explicit permission for. 🔐
