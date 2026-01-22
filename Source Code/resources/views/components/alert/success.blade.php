@props(['message' => session('success')])

@if ($message)
    <div class="mb-4">
        <div class="flex items-center justify-between px-4 py-3 rounded-md bg-green-100 text-green-800 shadow">
            <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span class="text-sm font-medium">{{ $message }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()"
                class="text-green-600 hover:text-green-900 focus:ring focus:ring-green-400"
                aria-label="Tutup notifikasi sukses">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif
