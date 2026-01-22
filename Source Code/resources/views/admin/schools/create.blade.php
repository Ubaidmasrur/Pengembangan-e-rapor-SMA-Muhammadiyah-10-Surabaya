<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li><a href="{{ route('admin.schools.index') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Sekolah</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Tambah</li>
            </ol>
        </nav>
    </header> --}}
    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#school-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to form</a>

            <h1 class="text-2xl font-bold mb-4" id="form-title">Tambah Sekolah</h1>

            {{-- Alert sukses standar --}}
            <x-alert.success />

            <section aria-labelledby="form-title">
                <form id="school-form" role="form" aria-labelledby="form-title" method="POST"
                    action="{{ route('admin.schools.store') }}" class="space-y-4">
                    @csrf

                    {{-- Nama Sekolah --}}
                    <x-form-input name="name" label="Nama Sekolah" type="text" :value="old('name')"
                        aria-label="Nama Sekolah" />

                    {{-- Alamat --}}
                    <x-form-textarea name="address" label="Alamat" rows="2" :value="old('address')"
                        aria-label="Alamat Sekolah" />

                    {{-- Telepon --}}
                    <x-form-input name="phone" label="Telepon" type="text" :value="old('phone')"
                        aria-label="Telepon Sekolah" />

                    {{-- Email --}}
                    <x-form-input name="email" label="Email" type="email" :value="old('email')"
                        aria-label="Email Sekolah" />

                    {{-- Kepala Sekolah --}}
                    <x-form-input name="principal_name" label="Nama Kepala Sekolah" type="text" :value="old('principal_name')"
                        aria-label="Nama Kepala Sekolah" />

                    {{-- Aksi --}}
                    <div class="flex justify-end gap-2">
                        <x-buttons.back :href="route('admin.schools.index')" label="Batal" />
                        <x-buttons.save label="Simpan" />
                    </div>
                </form>
            </section>
        </div>
    </main>
    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
