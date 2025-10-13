# Global Client Selection - Usage Guide

This document explains how to use the global client selection feature in your Laravel application.

## Overview

The global client selection feature allows you to select a client from the clients index page and access that selected client in any controller, view, or anywhere in your application.

## How It Works

1. **Session Storage**: When you select a client, the client ID and client data are stored in the session
2. **Global Availability**: The selected client is shared with all views via `AppServiceProvider`
3. **Helper Functions**: Easy-to-use helper functions are available throughout your application

## Selecting a Client

### From the Clients Index Page

1. Navigate to `/clients`
2. Click the "Select Client" button next to any client
3. The client will be selected and stored in the session
4. You'll see a confirmation message showing which client is selected
5. To clear the selection, click the "Clear Selection" button

## Accessing the Selected Client

### Method 1: Using Helper Functions (Recommended)

```php
// In any controller or anywhere in your application

// Check if a client is selected
if (hasSelectedClient()) {
    // Get the selected client object
    $client = selectedClient();
    echo $client->name;

    // Get just the client ID
    $clientId = selectedClientId();

    // Use in queries
    $personnel = Personnel::where('client_id', selectedClientId())->get();
}
```

### Method 2: Using Session Directly

```php
// In controllers
$selectedClientId = session('selected_client_id');
$selectedClient = session('selected_client');

if ($selectedClient) {
    echo $selectedClient->name;
}
```

### Method 3: Using the ClientHelper Class

```php
use App\Helpers\ClientHelper;

// Get selected client
$client = ClientHelper::getSelectedClient();

// Get selected client ID
$clientId = ClientHelper::getSelectedClientId();

// Check if client is selected
$hasClient = ClientHelper::hasSelectedClient();

// Set a client programmatically
ClientHelper::setSelectedClient($clientId); // or pass Client object

// Clear selected client
ClientHelper::clearSelectedClient();
```

### In Views (Blade Templates)

The selected client is automatically available in ALL views:

```blade
{{-- Check if a client is selected --}}
@if($selectedClient)
    <div>
        Currently viewing: {{ $selectedClient->name }}
    </div>
@endif

{{-- Or using the ID --}}
@if($selectedClientId)
    <p>Selected Client ID: {{ $selectedClientId }}</p>
@endif

{{-- Using helper functions in views --}}
@if(hasSelectedClient())
    <h2>{{ selectedClient()->name }}</h2>
@endif
```

## Example Use Cases

### Example 1: Filter Personnel by Selected Client

```php
// In PersonnelController.php

public function index()
{
    if (hasSelectedClient()) {
        // Show only personnel for the selected client
        $personnel = Personnel::where('client_id', selectedClientId())
            ->with(['user'])
            ->get();
    } else {
        // Show all personnel
        $personnel = Personnel::with(['client', 'user'])->get();
    }

    return view('personnel.index', compact('personnel'));
}
```

### Example 2: Auto-fill Client in Forms

```blade
{{-- In a create form --}}
<form method="POST" action="{{ route('personnel.store') }}">
    @csrf

    @if(hasSelectedClient())
        {{-- Auto-fill the client field --}}
        <input type="hidden" name="client_id" value="{{ selectedClientId() }}">
        <p>Creating personnel for: <strong>{{ selectedClient()->name }}</strong></p>
    @else
        {{-- Show client selection dropdown --}}
        <select name="client_id" required>
            <option value="">Select a client</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
        </select>
    @endif

    {{-- Rest of the form... --}}
</form>
```

### Example 3: Conditional Navigation

```blade
{{-- In your layout or navigation --}}
@if(hasSelectedClient())
    <nav>
        <a href="{{ route('clients.index') }}">
            Client: {{ selectedClient()->name }} (Change)
        </a>
        <a href="{{ route('personnel.index') }}">
            Personnel for {{ selectedClient()->name }}
        </a>
    </nav>
@else
    <div class="alert">
        <a href="{{ route('clients.index') }}">Select a client to get started</a>
    </div>
@endif
```

### Example 4: Programmatically Set Client

```php
// In any controller, you can set the client programmatically

use App\Helpers\ClientHelper;

public function someAction($clientId)
{
    // Set the client
    ClientHelper::setSelectedClient($clientId);

    // Or pass a Client model instance
    $client = Client::find($clientId);
    ClientHelper::setSelectedClient($client);

    return redirect()->route('dashboard');
}
```

## Session Data Stored

When a client is selected, the following data is stored in the session:

- `selected_client_id`: The ID of the selected client (integer)
- `selected_client`: The full Client model object with all its attributes

## Routes Available

- `POST /clients/{client}/select` - Select a client (route name: `clients.select`)
- `POST /clients/clear` - Clear the selected client (route name: `clients.clear`)

## Files Modified/Created

1. **Controllers**:

   - `app/Http/Controllers/ClientController.php` - Added `setSelectedClient()` and `clearSelectedClient()` methods

2. **Views**:

   - `resources/views/clients/index.blade.php` - Added client selection buttons and status display

3. **Providers**:

   - `app/Providers/AppServiceProvider.php` - Shares selected client with all views

4. **Helpers**:

   - `app/Helpers/ClientHelper.php` - Helper class for client operations
   - `app/helpers.php` - Global helper functions

5. **Configuration**:
   - `composer.json` - Added autoload for helpers.php
   - `routes/web.php` - Added routes for client selection

## Best Practices

1. **Always check if a client is selected** before accessing it to avoid null errors
2. **Use helper functions** for cleaner, more readable code
3. **Clear the selection** when it's no longer needed
4. **Consider the user experience**: Show which client is selected in your navigation/header

## Troubleshooting

### Helper functions not working?

Run: `docker-compose exec app composer dump-autoload`

### Session not persisting?

Check your `.env` file has correct session configuration:

```
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### Client data seems stale?

The client object is stored in the session. If you update a client's data in the database, you may need to re-select it to get the updated data.
