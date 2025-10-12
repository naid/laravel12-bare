@props([
    'name' => 'client_id',
    'id' => 'client_id',
    'selected' => null,
    'required' => false,
    'label' => 'Client',
    'placeholder' => 'Select a client'
])

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select 
        name="{{ $name }}" 
        id="{{ $id }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200']) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach(\App\Models\Client::orderBy('name')->get() as $client)
            <option value="{{ $client->id }}" {{ old($name, $selected) == $client->id ? 'selected' : '' }}>
                {{ $client->name }} - {{ $client->industry }}
            </option>
        @endforeach
    </select>
    
    @error($name)
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>