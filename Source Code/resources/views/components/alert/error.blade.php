@props(['message' => session('error')])

@if ($message)
    <div class="mb-4">
        <div class="flex items-center justify-between px-4 py-3 rounded-md bg-red-100 text-red-800 shadow">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="text-sm font-medium">{{ $message }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()"
                class="text-red-600 hover:text-red-900 focus:ring focus:ring-red-400" aria-label="Tutup notifikasi error">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif
