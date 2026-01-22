@props(['href', 'label' => 'Batal', 'icon' => 'fas fa-arrow-left', 'class' => ''])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 focus:ring focus:ring-gray-500 inline-flex items-center gap-2 ' . $class]) }}>
    <i class="{{ $icon }}"></i>
    {{ $label }}
</a>
