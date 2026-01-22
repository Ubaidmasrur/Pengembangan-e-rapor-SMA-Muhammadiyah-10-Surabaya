<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li><a href="{{ route('admin.school_classes.index') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Kelas</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Ubah</li>
            </ol>
        </nav>
    </header> --}}
    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#school-class-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to form</a>

            <h1 class="text-2xl font-bold mb-4" id="form-title">Ubah Data Kelas</h1>

            {{-- Alert sukses standar --}}
            <x-alert.success />

            <section aria-labelledby="form-title">
                <form id="school-class-form" role="form" aria-labelledby="form-title" method="POST"
                    action="{{ route('admin.school_classes.update', $school_class) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Sekolah --}}
                    <x-form-select name="school_id" label="Sekolah" :options="$schools->pluck('name', 'id')" :value="old('school_id', $school_class->school_id)"
                        placeholder="-- Pilih Sekolah --" aria-label="Sekolah" />

                    {{-- Nama Kelas --}}
                    <x-form-input name="name" label="Nama Kelas" type="text" :value="old('name', $school_class->name)"
                        aria-label="Nama Kelas" />

                    {{-- Aksi --}}
                    <div class="flex justify-end gap-2">
                        <x-buttons.back :href="route('admin.school_classes.index')" label="Batal" />
                        <x-buttons.save label="Perbarui" />
                    </div>
                </form>
            </section>
        </div>
    </main>
    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
