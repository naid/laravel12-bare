@extends('layouts.app', ['hideNavigation' => true, 'hideFooter' => true])

@section('title', 'Tailwind v4 Test')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md">
        <h1 class="text-3xl font-bold text-blue-600 mb-4">
            🎉 Tailwind CSS v4 is Working!
        </h1>
        <p class="text-gray-700 mb-4">
            This is styled with Tailwind v4. No config files needed!
        </p>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
            <p class="text-sm text-blue-700">
                ✓ Layout system is active<br>
                ✓ Tailwind CSS v4 loaded<br>
                ✓ Vite is compiling assets
            </p>
        </div>
        <div class="flex gap-2">
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                Primary Button
            </button>
            <button class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                Secondary
            </button>
        </div>
        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
                Go to Home
            </a>
        </div>
    </div>
</div>
@endsection

