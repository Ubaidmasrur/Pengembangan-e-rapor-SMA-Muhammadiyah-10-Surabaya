@props(['name', 'type' => 'text', 'label' => '', 'value' => '', 'id' => null])

@php
    $inputId = $id ?? $name;
@endphp

<div>
    {{-- Label --}}
    <label for="{{ $inputId }}" class="block font-medium text-base text-gray-700">
        {{ $label ?: ucfirst(str_replace('_', ' ', $name)) }}
    </label>

    {{-- Input Field --}}
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}" value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' =>
                'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 focus:outline-none ' .
                ($errors->has($name) ? 'border-red-500 ring-2 ring-red-400' : ''),
        ]) }}
        aria-label="{{ $label ?: ucfirst(str_replace('_', ' ', $name)) }}" />

    {{-- Error Message --}}
    @error($name)
        <x-input-error :message="$message" />
    @enderror
</div>
