# Laravel Blade Layout System - Examples & Guide

## Basic Layout Structure

Your main layout is located at: `resources/views/layouts/app.blade.php`

## How to Use the Layout

### 1. Basic Usage - Extending the Layout

```blade
@extends('layouts.app')

@section('title', 'Your Page Title')

@section('content')
    <h1>Your content goes here</h1>
@endsection
```

### 2. Custom Container Classes

Override the default container class for specific pages:

```blade
@extends('layouts.app')

@section('title', 'Full Width Page')

@section('container-class', 'container-fluid px-4')

@section('content')
    <!-- This will use custom container styling -->
@endsection
```

### 3. Adding Custom CSS/JavaScript

**Add page-specific styles:**

```blade
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom-page.css') }}">
@endpush

@section('content')
    <!-- Your content -->
@endsection
```

**Add page-specific scripts:**

```blade
@extends('layouts.app')

@section('content')
    <!-- Your content -->
@endsection

@push('scripts')
<script src="{{ asset('js/custom-page.js') }}"></script>
<script>
    console.log('Page-specific JavaScript');
</script>
@endpush
```

## Layout Features

### Navigation

The layout includes an automatic navigation bar that shows when user is authenticated:

- Dashboard link
- Users link
- Clients link
- User name display
- Logout button

### Flash Messages

The layout automatically displays flash messages:

**In your controller:**

```php
return redirect()->route('dashboard')->with('success', 'Operation successful!');
// or
return redirect()->back()->with('error', 'Something went wrong!');
```

### Responsive Design

All layouts are responsive using Tailwind CSS:

- Mobile-first approach
- Breakpoints: sm, md, lg, xl, 2xl
- Responsive navigation

## Component Sections Available

### @yield Sections:

- `title` - Page title (default: 'Laravel App')
- `container-class` - Override main container classes
- `content` - Main page content (required)

### @stack Sections:

- `styles` - Additional CSS files or inline styles
- `scripts` - Additional JavaScript files or inline scripts

## Creating Additional Layouts

You can create specialized layouts by extending the base layout:

**resources/views/layouts/guest.blade.php** (for non-authenticated pages):

```blade
@extends('layouts.app')

@section('container-class', 'min-h-screen flex items-center justify-center')

@section('content')
    <div class="max-w-md w-full">
        @yield('guest-content')
    </div>
@endsection
```

Then use it:

```blade
@extends('layouts.guest')

@section('title', 'Register')

@section('guest-content')
    <!-- Registration form -->
@endsection
```

## Common Blade Directives

### Extending & Sections

- `@extends('layout.name')` - Extends a parent layout
- `@section('name')...@endsection` - Define a section
- `@yield('name')` - Output a section
- `@parent` - Include parent section content

### Stacks (for CSS/JS)

- `@push('name')...@endpush` - Add to a stack
- `@stack('name')` - Output a stack

### Authentication

- `@auth...@endauth` - Show only to authenticated users
- `@guest...@endguest` - Show only to guests
- `{{ Auth::user()->name }}` - Get current user data

### Conditionals

- `@if(condition)...@endif`
- `@unless(condition)...@endunless`
- `@isset($variable)...@endisset`
- `@empty($variable)...@endempty`

### Loops

- `@foreach($items as $item)...@endforeach`
- `@for($i = 0; $i < 10; $i++)...@endfor`
- `@forelse($items as $item)...@empty...@endforelse`

### Including Partials

```blade
@include('partials.header')
@include('partials.sidebar', ['active' => 'dashboard'])
```

## Best Practices

1. **Keep layouts DRY** - Don't repeat navigation/footer in every view
2. **Use components** - Create reusable components for common UI elements
3. **Separate concerns** - Keep business logic in controllers
4. **Use @push for scripts** - Add page-specific JS at the bottom
5. **Flash messages** - Use session flashing for user feedback
6. **Consistent naming** - Use clear, descriptive section names

## Example: Complete Page with All Features

```blade
@extends('layouts.app')

@section('title', 'User Profile')

@push('styles')
<style>
    .profile-avatar { border-radius: 50%; }
</style>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-4">User Profile</h1>

    <div class="profile-avatar">
        <img src="{{ $user->avatar }}" alt="Avatar">
    </div>

    <p>{{ $user->name }}</p>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Profile page loaded');
    });
</script>
@endpush
```
