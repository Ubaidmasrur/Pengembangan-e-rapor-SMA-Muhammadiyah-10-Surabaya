@props(['message' => session('warning')])

@if ($message)
    <div class="mb-4">
        <div class="flex items-center justify-between px-4 py-3 rounded-md bg-yellow-100 text-yellow-800 shadow">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="text-sm font-medium">{{ $message }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()"
                class="text-yellow-600 hover:text-yellow-900 focus:ring focus:ring-yellow-400"
                aria-label="Tutup notifikasi peringatan">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif
