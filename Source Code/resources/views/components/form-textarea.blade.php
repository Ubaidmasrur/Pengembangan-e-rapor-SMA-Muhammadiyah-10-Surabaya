@props(['name', 'label' => null, 'rows' => 3, 'value' => ''])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block font-medium text-base text-gray-700">
            {{ $label }}
        </label>
    @endif

    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
        {{ $attributes->merge([
            'class' =>
                'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 focus:outline-none ' .
                ($errors->has($name) ? 'border-red-500 ring-2 ring-red-400' : ''),
        ]) }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <x-input-error :message="$message" />
    @enderror
</div>
