@props(['action', 'label' => 'Hapus', 'icon' => 'fas fa-trash-alt'])

<form method="POST" action="{{ $action }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
    @csrf
    @method('DELETE')
    <button type="submit"
        {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 font-semibold focus:outline-none focus:ring-2 focus:ring-red-400']) }}>
        <i class="{{ $icon }} mr-1"></i> {{ $label }}
    </button>
</form>
