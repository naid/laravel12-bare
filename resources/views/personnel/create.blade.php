@extends('layouts.app')

@section('title', 'Add Personnel')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Add New Personnel</h1>
        <p class="text-gray-600 mt-2">Create a new personnel record</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('personnel.store') }}" class="space-y-6">
            @csrf
            
            <!-- Client Dropdown Component -->
            <x-client-select 
                name="client_id" 
                label="Client" 
                required 
            />

            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="first_name" 
                    id="first_name" 
                    value="{{ old('first_name') }}"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('first_name') border-red-500 @enderror"
                >
                @error('first_name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="last_name" 
                    id="last_name" 
                    value="{{ old('last_name') }}"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('last_name') border-red-500 @enderror"
                >
                @error('last_name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('email') border-red-500 @enderror"
                >
                @error('email')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Phone Number
                </label>
                <input 
                    type="text" 
                    name="phone_number" 
                    id="phone_number" 
                    value="{{ old('phone_number') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('phone_number') border-red-500 @enderror"
                >
                @error('phone_number')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Position and Department in Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                        Position
                    </label>
                    <input 
                        type="text" 
                        name="position" 
                        id="position" 
                        value="{{ old('position') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('position') border-red-500 @enderror"
                    >
                    @error('position')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Department
                    </label>
                    <input 
                        type="text" 
                        name="department" 
                        id="department" 
                        value="{{ old('department') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('department') border-red-500 @enderror"
                    >
                    @error('department')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Hire Date -->
            <div>
                <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Hire Date
                </label>
                <input 
                    type="date" 
                    name="hire_date" 
                    id="hire_date" 
                    value="{{ old('hire_date') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('hire_date') border-red-500 @enderror"
                >
                @error('hire_date')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-4">
                <a 
                    href="{{ route('personnel.index') }}" 
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200"
                >
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200"
                >
                    Create Personnel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection