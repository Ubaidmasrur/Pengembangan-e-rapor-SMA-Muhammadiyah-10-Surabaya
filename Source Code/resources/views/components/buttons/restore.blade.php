@props(['action', 'label' => 'Pulihkan', 'icon' => 'fas fa-undo'])

<form method="POST" action="{{ $action }}" class="inline">
    @csrf
    <button type="submit"
        {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200 font-semibold focus:outline-none focus:ring-2 focus:ring-green-400']) }}>
        <i class="{{ $icon }} mr-1"></i> {{ $label }}
    </button>
</form>
