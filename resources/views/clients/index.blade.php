@extends('layouts.app')

@section('title', 'Clients List')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Clients</h1>
    <p class="text-gray-600 mt-2">Manage all clients</p>
</div>

{{-- Display selected client if one is selected --}}
@if(session('selected_client'))
<div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg shadow-md">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-blue-800">Currently Selected Client</p>
            <p class="text-xl font-bold text-blue-900 mt-1">{{ session('selected_client')->name }}</p>
        </div>
        <form action="{{ route('clients.clear') }}" method="POST">
            @csrf
            <button 
                type="submit" 
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 shadow-md"
            >
                Clear Selection
            </button>
        </form>
    </div>
</div>
@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Industry
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Services
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Action
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($clients as $client)
                <tr class="hover:bg-gray-50 {{ session('selected_client_id') == $client->id ? 'bg-blue-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $client->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $client->industry }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $client->services_provided }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if(session('selected_client_id') == $client->id)
                            <span class="text-blue-600 font-semibold">✓ Selected</span>
                        @else
                            <form action="{{ route('clients.select', $client->id) }}" method="POST" class="inline">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition"
                                >
                                    Select Client
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        No clients found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
