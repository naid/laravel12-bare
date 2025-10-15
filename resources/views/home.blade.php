@extends('layouts.app')

@section('title', 'Home')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Welcome to Laravel 12</h1>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Hero Section --}}
    <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">
            Your Laravel App is Ready! 🚀
        </h2>
        <p class="text-xl text-gray-600 mb-8">
            Built with Laravel 12, Tailwind CSS v4, and Docker
        </p>
    </div>

    {{-- Feature Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        {{-- Card 1 --}}
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
            <div class="text-blue-500 mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Fast Development</h3>
            <p class="text-gray-600">
                Vite provides lightning-fast hot module replacement for instant feedback during development.
            </p>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
            <div class="text-green-500 mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Beautiful UI</h3>
            <p class="text-gray-600">
                Tailwind CSS v4 makes it easy to build modern, responsive interfaces with utility classes.
            </p>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
            <div class="text-purple-500 mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Docker Ready</h3>
            <p class="text-gray-600">
                Fully containerized with Docker, ensuring consistency across all development environments.
            </p>
        </div>
    </div>

    {{-- Quick Links Section --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Quick Links</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ url('/test') }}" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition">
                <span class="text-blue-500 mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <div>
                    <h4 class="font-semibold text-gray-900">Test Tailwind CSS</h4>
                    <p class="text-sm text-gray-600">Verify Tailwind v4 is working</p>
                </div>
            </a>

            <a href="http://localhost:8080" target="_blank" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition">
                <span class="text-green-500 mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </span>
                <div>
                    <h4 class="font-semibold text-gray-900">phpMyAdmin</h4>
                    <p class="text-sm text-gray-600">Manage your database</p>
                </div>
            </a>

            <a href="{{ url('/md') }}" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition">
                <span class="text-purple-500 mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </span>
                <div>
                    <h4 class="font-semibold text-gray-900">Documentation</h4>
                    <p class="text-sm text-gray-600">Read setup guides and tutorials</p>
                </div>
            </a>

            <a href="https://laravel.com/docs" target="_blank" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition">
                <span class="text-red-500 mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <div>
                    <h4 class="font-semibold text-gray-900">Laravel Docs</h4>
                    <p class="text-sm text-gray-600">Official documentation</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Status Section --}}
    <div class="mt-12 bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">System Status</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-green-500 text-2xl mb-2">✓</div>
                <div class="text-sm font-semibold text-gray-900">Laravel 12</div>
                <div class="text-xs text-gray-600">Running</div>
            </div>
            <div class="text-center">
                <div class="text-green-500 text-2xl mb-2">✓</div>
                <div class="text-sm font-semibold text-gray-900">Tailwind v4</div>
                <div class="text-xs text-gray-600">Active</div>
            </div>
            <div class="text-center">
                <div class="text-green-500 text-2xl mb-2">✓</div>
                <div class="text-sm font-semibold text-gray-900">Vite</div>
                <div class="text-xs text-gray-600">Compiled</div>
            </div>
            <div class="text-center">
                <div class="text-green-500 text-2xl mb-2">✓</div>
                <div class="text-sm font-semibold text-gray-900">Docker</div>
                <div class="text-xs text-gray-600">Running</div>
            </div>
        </div>
    </div>
</div>
@endsection

