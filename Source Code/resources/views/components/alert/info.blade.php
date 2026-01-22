@props(['message' => session('info')])

@if ($message)
    <div class="mb-4">
        <div class="flex items-center justify-between px-4 py-3 rounded-md bg-blue-100 text-blue-800 shadow">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <span class="text-sm font-medium">{{ $message }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()"
                class="text-blue-600 hover:text-blue-900 focus:ring focus:ring-blue-400"
                aria-label="Tutup notifikasi info">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif
