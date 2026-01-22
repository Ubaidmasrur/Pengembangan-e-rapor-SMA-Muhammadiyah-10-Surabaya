<x-app-layout>
    <div class="max-w-xl mx-auto py-6 px-4">
        <h1 class="text-xl font-semibold mb-4">Edit Pengguna</h1>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama (readonly dari entitas) --}}
            <x-form-input name="name" label="Nama" :value="$user->name" readonly />

            {{-- Role (readonly) --}}
            <x-form-input name="role_view" label="Peran" :value="ucfirst($user->role)" readonly />
            <input type="hidden" name="role" value="{{ $user->role }}">

            {{-- Email --}}
            <x-form-input name="email" label="Email" type="email" :value="old('email', $user->email)" />

            {{-- Password (opsional) --}}
            <x-form-input name="password" label="Password Baru (opsional)" type="password" />
            <x-form-input name="password_confirmation" label="Konfirmasi Password" type="password" />

            <div class="flex justify-end gap-2 mt-4">
                <x-buttons.back :href="route('admin.users.index')" />
                <x-buttons.save label="Perbaharui" />
            </div>
        </form>

    </div>
</x-app-layout>
