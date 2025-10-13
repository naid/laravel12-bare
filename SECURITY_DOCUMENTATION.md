# Security Documentation - Global Client Selection

## Overview

The global client selection feature is now **fully secured** with role-based access control (RBAC) and authorization policies. Users can only select and access clients they have explicit permission to view.

## Security Features Implemented

### 1. **Role-Based Access Control (RBAC)**

Three user roles are supported:

- **Admin** (`role = 'admin'`)

  - Can access ALL clients
  - Can create, update, and delete any client
  - Can select any client globally
  - Bypasses all client-specific restrictions

- **Manager** (`role = 'manager'`)

  - Can only access assigned clients
  - Can create new clients
  - Can update clients with 'write' or 'admin' access level
  - Can select only assigned clients

- **User** (`role = 'user'`)
  - Can only access assigned clients (read-only by default)
  - Cannot create or update clients
  - Can select only assigned clients

### 2. **Client-User Access Control**

Access is controlled through the `client_user` pivot table with three access levels:

- **read** - Can view client data only
- **write** - Can view and update client data
- **admin** - Can view, update, and manage client (full access)

### 3. **Authorization Policy (ClientPolicy)**

All client operations go through the `ClientPolicy` which enforces:

- ✅ Users can only VIEW clients they have access to
- ✅ Users can only SELECT clients they have permission for
- ✅ Authorization failures return proper error messages
- ✅ Admins bypass restrictions and can access everything

### 4. **Middleware Validation**

`ValidateSelectedClient` middleware runs on EVERY request to:

- Verify the selected client still exists
- Verify the user still has permission to access it
- Auto-clear invalid selections from session
- Display warning messages when access is revoked

### 5. **Controller-Level Security**

The `ClientController` enforces:

```php
// Only show clients the user has access to
public function index()
{
    $this->authorize('viewAny', Client::class);

    if ($user->isAdmin()) {
        $clients = Client::all(); // Admins see all
    } else {
        $clients = $user->clients()->get(); // Users see only assigned
    }
}

// Only allow selecting authorized clients
public function setSelectedClient($clientId)
{
    $client = Client::findOrFail($clientId);
    $this->authorize('select', $client); // SECURITY CHECK

    session(['selected_client_id' => $client->id]);
}
```

## Database Schema

### Users Table (with role field)

```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    role VARCHAR(20) DEFAULT 'user', -- admin, manager, or user
    password VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Client-User Pivot Table

```sql
CREATE TABLE client_user (
    id BIGINT PRIMARY KEY,
    client_id BIGINT FOREIGN KEY REFERENCES clients(id) ON DELETE CASCADE,
    user_id BIGINT FOREIGN KEY REFERENCES users(id) ON DELETE CASCADE,
    access_level VARCHAR(20) DEFAULT 'read', -- read, write, or admin
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(client_id, user_id)
);
```

## Setting Up Security

### 1. Run Migrations

```bash
docker-compose exec app php artisan migrate
```

This will add:

- `role` field to users table
- `client_user` pivot table for access control

### 2. Seed Test Data

```bash
docker-compose exec app php artisan db:seed --class=UserClientSeeder
```

This creates three test users:

- **admin@example.com** (password: password) - Admin role
- **manager@example.com** (password: password) - Manager role
- **user@example.com** (password: password) - User role

### 3. Assign Users to Clients

#### Programmatically:

```php
use App\Models\User;
use App\Models\Client;

$user = User::find($userId);
$client = Client::find($clientId);

// Attach user to client with access level
$user->clients()->attach($client->id, ['access_level' => 'read']);

// Or sync multiple clients
$user->clients()->sync([
    1 => ['access_level' => 'write'],
    2 => ['access_level' => 'read'],
    3 => ['access_level' => 'admin'],
]);
```

#### Via Database:

```sql
INSERT INTO client_user (client_id, user_id, access_level, created_at, updated_at)
VALUES (1, 2, 'write', NOW(), NOW());
```

## Usage Examples

### Check User Permissions in Controllers

```php
use Illuminate\Support\Facades\Gate;

// Check if user can view a client
if (Gate::allows('view', $client)) {
    // User can view this client
}

// Check if user can select a client
if (Gate::allows('select', $client)) {
    // User can select this client
}

// Check if user can update a client
if (Gate::allows('update', $client)) {
    // User can update this client
}

// Or use authorize() to throw 403 if denied
$this->authorize('view', $client);
```

### Check User Role

```php
$user = auth()->user();

if ($user->isAdmin()) {
    // User is admin
}

if ($user->isManager()) {
    // User is manager
}

if ($user->canAccessClient($client)) {
    // User has access to this specific client
}

// Get all accessible clients for a user
$accessibleClients = $user->accessibleClients()->get();
```

### In Blade Templates

```blade
@can('view', $client)
    <a href="{{ route('client.show', $client) }}">View Client</a>
@endcan

@can('update', $client)
    <a href="{{ route('client.edit', $client) }}">Edit Client</a>
@endcan

@can('select', $client)
    <form action="{{ route('clients.select', $client) }}" method="POST">
        @csrf
        <button type="submit">Select Client</button>
    </form>
@else
    <span class="text-muted">No Access</span>
@endcan
```

## Security Best Practices

### ✅ DO:

1. **Always use authorization checks** before allowing client selection
2. **Use the ClientPolicy** for all client-related permissions
3. **Validate selected client on every request** (middleware does this)
4. **Assign explicit permissions** through the pivot table
5. **Use role hierarchy** (admin > manager > user)
6. **Log permission changes** for audit trails

### ❌ DON'T:

1. **Don't bypass authorization** with direct queries
2. **Don't trust session data** without validation
3. **Don't expose client IDs** to unauthorized users
4. **Don't use client data** without checking permissions
5. **Don't assume permissions persist** (they can be revoked)

## Attack Vectors Prevented

### 1. **Unauthorized Client Selection**

- ❌ User tries to select client ID they don't have access to
- ✅ Authorization check denies the action, returns 403 error

### 2. **Session Hijacking**

- ❌ Attacker gets session with selected client
- ✅ Middleware validates on every request, clears invalid selections

### 3. **Privilege Escalation**

- ❌ Regular user tries to access admin-only client
- ✅ Policy checks role and permissions, denies access

### 4. **Direct URL Manipulation**

- ❌ User posts to `/clients/999/select` for unauthorized client
- ✅ Controller authorization check fails before selection

### 5. **Permission Revocation**

- ❌ User's access removed but client still selected
- ✅ Middleware auto-clears selection on next request

## Error Handling

### Authorization Failures

When authorization fails, Laravel returns:

- **403 Forbidden** HTTP status
- Custom error message from policy
- User-friendly error page

### Session Validation

When selected client becomes invalid:

- Session is automatically cleared
- Warning message is flashed to user
- User is redirected safely

## Monitoring & Auditing

### Log Client Access

```php
use Illuminate\Support\Facades\Log;

public function setSelectedClient($clientId)
{
    $client = Client::findOrFail($clientId);
    $this->authorize('select', $client);

    // Log the selection
    Log::info('Client selected', [
        'user_id' => auth()->id(),
        'client_id' => $client->id,
        'ip' => request()->ip(),
        'timestamp' => now()
    ]);

    session(['selected_client_id' => $client->id]);
}
```

### Track Permission Changes

```php
// When assigning/revoking access
$user->clients()->attach($clientId, ['access_level' => 'read']);

Log::info('Client access granted', [
    'user_id' => $user->id,
    'client_id' => $clientId,
    'access_level' => 'read',
    'granted_by' => auth()->id()
]);
```

## Testing Security

### Test User Access

```bash
# Login as different users
admin@example.com     # Should see ALL clients
manager@example.com   # Should see assigned clients only
user@example.com      # Should see assigned clients only (read-only)
```

### Test Authorization

1. Login as `user@example.com`
2. Try to select a client you don't have access to
3. Should receive 403 error
4. Try to select an assigned client
5. Should succeed

### Test Middleware Validation

1. Login as user with client access
2. Select a client
3. Admin removes your access (in database)
4. Refresh any page
5. Selected client should be auto-cleared with warning

## Troubleshooting

### "403 Forbidden" when selecting client

- Check if user has access in `client_user` table
- Verify user role is correct
- Check ClientPolicy logic

### Selected client keeps getting cleared

- Check middleware is not being too aggressive
- Verify client still exists in database
- Check user still has access permissions

### Users can't see any clients

- Verify role is set correctly on user
- Check `client_user` pivot table has entries
- Ensure ClientPolicy `viewAny()` returns true

## Migration from Unsecured to Secured

If you already have the unsecured version running:

1. **Run new migrations** to add role and pivot table
2. **Update all existing users** to have appropriate roles
3. **Assign client access** to all existing users
4. **Test authorization** before deploying to production
5. **Clear all sessions** to force re-validation

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Seed roles and permissions
docker-compose exec app php artisan db:seed --class=UserClientSeeder

# Clear sessions
docker-compose exec app php artisan session:clear
```

## Summary

The global client selection feature is now **enterprise-grade secure** with:

- ✅ Role-based access control
- ✅ Policy-based authorization
- ✅ Middleware validation on every request
- ✅ Granular access levels (read/write/admin)
- ✅ Automatic cleanup of invalid selections
- ✅ Protection against common attack vectors
- ✅ Comprehensive error handling
- ✅ Audit trail capability

**Users can ONLY select and access clients they have explicit permission for.**
