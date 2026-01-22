@props(['href', 'label' => 'Tambah', 'icon' => 'fas fa-plus'])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500']) }}>
    <i class="{{ $icon }} mr-1"></i> {{ $label }}
</a>
