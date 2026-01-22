@props(['href', 'label' => 'Ubah', 'icon' => 'fas fa-edit'])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-400']) }}>
    <i class="{{ $icon }} mr-1"></i> {{ $label }}
</a>
