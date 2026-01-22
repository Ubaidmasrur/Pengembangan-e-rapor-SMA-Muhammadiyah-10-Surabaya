<x-app-layout>
    <div class="max-w-xl mx-auto py-6 px-4">
        <h1 class="text-xl font-semibold mb-4">Tambah Pengguna</h1>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            {{-- Pilih Peran --}}
            <x-form-select name="role" id="role-select" label="Peran" :options="['siswa' => 'Siswa', 'guru' => 'Guru']" :value="old('role')" />

            {{-- Pilih Entitas (Siswa/Guru) --}}
            <div id="entity-selector-container"></div>

            {{-- Email --}}
            <x-form-input name="email" label="Email" type="email" :value="old('email')" />

            {{-- Password --}}
            <x-form-input name="password" label="Password" type="password" />

            {{-- Konfirmasi Password --}}
            <x-form-input name="password_confirmation" label="Konfirmasi Password" type="password" />

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-2 mt-4">
                <x-buttons.back :href="route('admin.users.index')" />
                <x-buttons.save />
            </div>
        </form>

        {{-- Hidden values for JS --}}
        <input type="hidden" id="old-role" value="{{ old('role') }}">
        <input type="hidden" id="old-entity-id" value="{{ old('entity_id') }}">

    </div>

    {{-- Dynamic JS Loader --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleSelect = document.getElementById('role-select');
            const container = document.getElementById('entity-selector-container');

            const oldRole = document.getElementById('old-role')?.value;
            const oldEntityId = document.getElementById('old-entity-id')?.value;

            async function loadEntities(role, selectedId = null) {
                try {
                    const response = await fetch(`{{ route('admin.users.get-entities') }}?role=${role}`);
                    const data = await response.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        container.innerHTML = `
                            <p class="text-sm text-red-600">Tidak ada ${role} yang tersedia atau belum memiliki akun.</p>
                        `;
                        return;
                    }

                    const options = data.map(item => {
                        const selected = selectedId && selectedId == item.id ? 'selected' : '';
                        return `<option value="${item.id}" ${selected}>${item.name}</option>`;
                    }).join('');

                    container.innerHTML = `
                        <div>
                            <label for="entity_id" class="block font-medium text-base text-gray-700">
                                Pilih ${role.charAt(0).toUpperCase() + role.slice(1)}
                            </label>
                            <select name="entity_id" id="entity_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 focus:outline-none @error('entity_id') border-red-500 ring-2 ring-red-400 @enderror">
                                <option value="">-- Pilih --</option>
                                ${options}
                            </select>
                            @if ($errors->has('entity_id'))
                                <x-input-error :message="$errors->first('entity_id')" />
                            @endif
                        </div>
                    `;
                } catch (err) {
                    console.error(err);
                    container.innerHTML = `<p class="text-sm text-red-600">Gagal memuat data ${role}.</p>`;
                }
            }

            roleSelect?.addEventListener('change', (e) => {
                const selectedRole = e.target.value;
                if (selectedRole) {
                    loadEntities(selectedRole);
                } else {
                    container.innerHTML = '';
                }
            });

            if (oldRole) {
                roleSelect.value = oldRole;
                loadEntities(oldRole, oldEntityId);
            }
        });
    </script>
</x-app-layout>
