@props([
    'name' => 'role',
    'id' => 'role',
    'selected' => null,
    'required' => false,
    'label' => 'Role',
    'placeholder' => 'Select a role'
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
        <option value="admin" {{ old($name, $selected) == 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="manager" {{ old($name, $selected) == 'manager' ? 'selected' : '' }}>Manager</option>
        <option value="user" {{ old($name, $selected) == 'user' ? 'selected' : '' }}>User</option>
    </select>
    
    @error($name)
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>

