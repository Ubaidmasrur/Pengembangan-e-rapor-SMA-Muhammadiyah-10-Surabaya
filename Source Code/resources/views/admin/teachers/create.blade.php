<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li><a href="{{ route('admin.teachers.index') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Guru</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Tambah</li>
            </ol>
        </nav>
    </header> --}}

    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#teacher-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to form</a>
            <h1 class="text-2xl font-bold mb-4" id="form-title">Tambah Guru</h1>

            <section aria-labelledby="form-title">
                <form id="teacher-form" role="form" aria-labelledby="form-title" method="POST"
                    action="{{ route('admin.teachers.store') }}" class="space-y-4">
                    @csrf

                    {{-- Nama Guru --}}
                    <x-form-input name="name" label="Nama Guru" :value="old('name')" />

                    {{-- NIP --}}
                    <x-form-input name="nip" label="NIP" :value="old('nip')" />

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-2 mt-4">
                        <x-buttons.back :href="route('admin.teachers.index')" />
                        <x-buttons.save />
                    </div>
                </form>
            </section>
        </div>
    </main>

    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
