@props([
    'name' => 'client_ids',
    'id' => 'client_ids',
    'selected' => [],
    'required' => false,
    'label' => 'Clients',
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
        name="{{ $name }}[]" 
        id="{{ $id }}"
        multiple
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200']) }}
        style="min-height: 120px;"
    >
        @foreach(\App\Models\Client::orderBy('name')->get() as $client)
            <option value="{{ $client->id }}" 
                {{ in_array($client->id, old($name, is_array($selected) ? $selected : [])) ? 'selected' : '' }}>
                {{ $client->name }} - {{ $client->industry }}
            </option>
        @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Cmd on Mac) to select multiple clients</p>
    
    @error($name)
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>

