# Main Layout Usage Guide

Your Laravel application now has a comprehensive main layout (`resources/views/layouts/app.blade.php`) that all your pages can extend.

## Layout Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php              # Main layout
│   └── partials/
│       ├── navigation.blade.php   # Navigation bar
│       └── footer.blade.php       # Footer
```

## Features Included

✅ **Navigation Bar** - Responsive navbar with user authentication state  
✅ **Flash Messages** - Success, error, info, and warning notifications  
✅ **Page Headers** - Optional header section for page titles  
✅ **Footer** - Site-wide footer with links  
✅ **Meta Tags** - SEO and CSRF token  
✅ **Stacks** - For adding custom CSS/JS to specific pages  
✅ **Alpine.js** - For dropdown interactions

---

## Basic Usage

### Simple Page

```blade
@extends('layouts.app')

@section('title', 'My Page Title')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold">Welcome!</h1>
    <p class="mt-4">This is my content.</p>
</div>
@endsection
```

### Page with Header Section

```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <p>Your dashboard content here...</p>
</div>
@endsection
```

---

## Available Sections

### 1. **@section('title')**

Sets the page title (appears in browser tab)

```blade
@section('title', 'My Page')
```

### 2. **@section('header')**

Optional page header with white background and shadow

```blade
@section('header')
    <h1 class="text-2xl font-bold">Page Title</h1>
@endsection
```

### 3. **@section('content')** ⚠️ Required

Main content area (this is required!)

```blade
@section('content')
    <div class="container mx-auto">
        <!-- Your content -->
    </div>
@endsection
```

---

## Adding Custom CSS/JavaScript

### Add Page-Specific CSS

```blade
@extends('layouts.app')

@push('styles')
<style>
    .custom-class {
        color: red;
    }
</style>
@endpush

@section('content')
    <!-- Your content -->
@endsection
```

### Add Page-Specific JavaScript

```blade
@extends('layouts.app')

@push('scripts')
<script>
    console.log('Page-specific JavaScript');

    document.addEventListener('DOMContentLoaded', function() {
        // Your code here
    });
</script>
@endpush

@section('content')
    <!-- Your content -->
@endsection
```

### Add Meta Tags

```blade
@extends('layouts.app')

@push('meta')
    <meta name="description" content="My page description">
    <meta property="og:title" content="My Page">
@endpush

@section('content')
    <!-- Your content -->
@endsection
```

---

## Hiding Navigation or Footer

### Hide Navigation

```blade
@extends('layouts.app', ['hideNavigation' => true])

@section('content')
    <!-- Content without navigation -->
@endsection
```

### Hide Footer

```blade
@extends('layouts.app', ['hideFooter' => true])

@section('content')
    <!-- Content without footer -->
@endsection
```

### Hide Both (Clean Layout)

```blade
@extends('layouts.app', ['hideNavigation' => true, 'hideFooter' => true])

@section('content')
    <!-- Content without navigation and footer (e.g., login page) -->
@endsection
```

---

## Flash Messages

The layout automatically displays flash messages. In your controller, use:

### Success Message

```php
return redirect()->route('dashboard')->with('success', 'Operation completed successfully!');
```

### Error Message

```php
return redirect()->back()->with('error', 'Something went wrong!');
```

### Info Message

```php
return redirect()->route('home')->with('info', 'Here is some information.');
```

### Warning Message

```php
return redirect()->route('settings')->with('warning', 'Please update your profile.');
```

### Example in Controller

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|max:255',
    ]);

    // Save data...

    return redirect()
        ->route('dashboard')
        ->with('success', 'Data saved successfully!');
}
```

---

## Navigation Customization

### Add More Navigation Links

Edit `resources/views/layouts/partials/navigation.blade.php`:

```blade
<a href="{{ route('users.index') }}"
   class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('users.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium transition">
    Users
</a>
```

### Add More Dropdown Items

In the user dropdown section:

```blade
<a href="{{ route('profile.edit') }}"
   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
    Profile
</a>
<a href="{{ route('settings') }}"
   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
    Settings
</a>
```

---

## Example Pages

### 1. Login Page (Clean Layout)

```blade
@extends('layouts.app', ['hideNavigation' => true, 'hideFooter' => true])

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <!-- Form fields -->
        </form>
    </div>
</div>
@endsection
```

### 2. Dashboard Page

```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Dashboard cards -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Card 1</h3>
            <p class="text-3xl font-bold mt-2">100</p>
        </div>
    </div>
</div>
@endsection
```

### 3. List Page with Search

```blade
@extends('layouts.app')

@section('title', 'Users')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Users</h1>
        <a href="{{ route('users.create') }}"
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
            Add User
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search form -->
    <div class="mb-6">
        <input type="text"
               placeholder="Search users..."
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

### 4. Form Page

```blade
@extends('layouts.app')

@section('title', 'Create User')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email"
                       name="email"
                       id="email"
                       value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('users.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
```

---

## Understanding Layout Components

### How Navigation Detects Active Links

The navigation uses `request()->routeIs()` to highlight active links:

```blade
{{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500' }}
```

### How Flash Messages Work

The layout checks session for flash messages:

```blade
@if(session('success'))
    <!-- Display success message -->
@endif
```

In controllers, set with:

```php
->with('success', 'Message here')
```

### Alpine.js Dropdown

The user dropdown uses Alpine.js for interactivity:

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

---

## Best Practices

1. **Always use flash messages for user feedback**

   ```php
   return redirect()->with('success', 'Action completed!');
   ```

2. **Use the header section for page titles**

   ```blade
   @section('header')
       <h1>Page Title</h1>
   @endsection
   ```

3. **Maintain consistent container widths**

   ```blade
   <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
   ```

4. **Hide navigation/footer only when necessary** (login, register pages)

5. **Use @error directive for form validation errors**
   ```blade
   @error('field')
       <p class="text-red-500">{{ $message }}</p>
   @enderror
   ```

---

## Troubleshooting

### Dropdown not working?

Make sure Alpine.js is loaded. It's included via CDN in the navigation partial.

### Styles not applying?

1. Make sure `npm run dev` is running
2. Check that `@vite()` directive is in the layout
3. Clear browser cache

### Navigation not showing?

Check if you've hidden it:

```blade
@extends('layouts.app', ['hideNavigation' => true])
```

---

## Summary

Your main layout provides:

- ✅ Consistent look across all pages
- ✅ Automatic flash message display
- ✅ Responsive navigation with user dropdown
- ✅ Flexible content sections
- ✅ Easy to customize and extend

All your pages should extend this layout for consistency!
