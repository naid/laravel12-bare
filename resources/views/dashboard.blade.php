@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8 bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800">Welcome, {{ Auth::user()->name }}!</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <a href="{{ route('users.index')}}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-200">
        <h2 class="text-xl font-semibold text-blue-600">Users List</h2>
        <p class="text-gray-600 mt-2">Manage all users</p>
    </a>
    <a href="{{ route('clients.index')}}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-200">
        <h2 class="text-xl font-semibold text-blue-600">Clients List</h2>
        <p class="text-gray-600 mt-2">Manage all clients</p>
    </a>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <p class="text-green-600 font-semibold mb-2">✓ You are logged in!</p>
    <p class="text-gray-700">Your email: <span class="font-medium text-gray-900">{{ Auth::user()->email }}</span></p>
</div>
@endsection