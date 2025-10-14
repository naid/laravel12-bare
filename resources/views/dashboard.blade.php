@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8 bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800">Welcome, {{ Auth::user()->name }}!</h1>
</div>

{{-- Display selected client if one is selected --}}
@if(hasSelectedClient())
<div class="mb-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg shadow-md">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-blue-800">Currently Selected Client</h2>
            <p class="text-2xl font-bold text-blue-900 mt-2">{{ selectedClient()->name }}</p>
            <p class="text-gray-600 mt-1">Industry: {{ selectedClient()->industry }}</p>
            <p class="text-gray-600">Services: {{ selectedClient()->services_provided }}</p>
        </div>
        <form action="{{ route('clients.clear') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 shadow-md font-semibold">
                Clear Selection
            </button>
        </form>
    </div>
</div>
@else
<div class="mb-8 bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold text-yellow-800">No Client Selected</h2>
    <p class="text-gray-700 mt-2">
        <a href="{{ route('clients.index') }}" class="text-blue-600 hover:underline font-semibold">
            Click here to select a client
        </a>
    </p>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('users.index')}}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-200">
        <h2 class="text-xl font-semibold text-blue-600">Users List</h2>
        <p class="text-gray-600 mt-2">Manage all users</p>
    </a>
    <a href="{{ route('clients.index')}}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-200">
        <h2 class="text-xl font-semibold text-blue-600">Clients List</h2>
        <p class="text-gray-600 mt-2">Manage all clients</p>
    </a>
    <a href="{{ route('personnel.index')}}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-200">
        <h2 class="text-xl font-semibold text-blue-600">Personnel List</h2>
        <p class="text-gray-600 mt-2">Manage personnel records</p>
    </a>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <p class="text-green-600 font-semibold mb-2">✓ You are logged in!</p>
    <p class="text-gray-700">Your email: <span class="font-medium text-gray-900">{{ Auth::user()->email }}</span></p>
</div>
@endsection