@props(['name', 'id' => null, 'label' => '', 'options' => [], 'value' => ''])

@php
    $id = $id ?? $name;
@endphp

<div>
    {{-- Label --}}
    <label for="{{ $id }}" class="block font-medium text-base text-gray-700">
        {{ $label ?: ucfirst(str_replace('_', ' ', $name)) }}
    </label>

    {{-- Select --}}
    <select name="{{ $name }}" id="{{ $id }}"
        {{ $attributes->merge([
            'class' =>
                'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 focus:outline-none ' .
                ($errors->has($name) ? 'border-red-500 ring-2 ring-red-400' : ''),
        ]) }}
        aria-label="{{ $label ?: ucfirst(str_replace('_', ' ', $name)) }}">
        <option value="">-- Pilih --</option>
        @foreach ($options as $key => $label)
            <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    {{-- Error --}}
    @error($name)
        <x-input-error :message="$message" />
    @enderror
</div>
