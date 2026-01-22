@props([
    'label' => 'Simpan',
    'icon' => 'fas fa-save',
    'type' => 'submit',
    'class' => '',
])

<button type="{{ $type }}"
    {{ $attributes->merge(['class' => 'bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:ring focus:ring-blue-500 inline-flex items-center gap-2 ' . $class]) }}>
    <i class="{{ $icon }}"></i>
    {{ $label }}
</button>
