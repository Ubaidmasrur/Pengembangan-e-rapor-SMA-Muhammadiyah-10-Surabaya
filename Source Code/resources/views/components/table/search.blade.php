@props([
    'action' => request()->url(),
    'placeholder' => 'Cari...',
    'name' => 'q',
    'value' => request('q'),
    'ariaLabel' => 'Form pencarian',
])

<form method="GET" action="{{ $action }}" class="flex items-center gap-2 w-full sm:w-auto mb-4" role="search"
    aria-label="{{ $ariaLabel }}">
    <label for="search-{{ $name }}" class="sr-only">{{ $placeholder }}</label>

    <input type="text" name="{{ $name }}" id="search-{{ $name }}" value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="px-4 py-2 rounded border border-gray-300 focus:ring focus:ring-blue-200 focus:outline-none w-full sm:w-64"
        aria-label="{{ $placeholder }}" />

    <button type="submit"
        class="px-4 py-2 rounded bg-blue-600 text-white font-semibold hover:bg-blue-700 transition flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        aria-label="Cari">
        <i class="fas fa-search"></i>
        Cari
    </button>
</form>
