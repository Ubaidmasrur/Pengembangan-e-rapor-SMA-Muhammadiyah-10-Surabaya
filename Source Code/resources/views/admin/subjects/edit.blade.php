<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li><a href="{{ route('admin.subjects.index') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Mata Pelajaran</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Ubah</li>
            </ol>
        </nav>
    </header> --}}

    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#subject-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to form</a>
            <h1 class="text-2xl font-bold mb-4" id="form-title">Ubah Data Mata Pelajaran</h1>

            <section aria-labelledby="form-title">
                <form id="subject-form" role="form" aria-labelledby="form-title" method="POST"
                    action="{{ route('admin.subjects.update', $subject) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Nama Mapel --}}
                    <x-form-input name="name" label="Nama Mapel" :value="old('name', $subject->name)" />

                    {{-- Tipe --}}
                    <x-form-select name="type" label="Tipe" :options="['umum' => 'Umum', 'khusus' => 'Khusus', 'ekstra' => 'Ekstra']" :value="old('type', $subject->type)" />

                    {{-- Nilai Minimum --}}
                    <x-form-input name="min_grade" label="Nilai Minimum" type="number" step="0.01"
                        :value="old('min_grade', $subject->min_grade)" />

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-2 mt-4">
                        <x-buttons.back :href="route('admin.subjects.index')" />
                        <x-buttons.save label="Perbaharui" />
                    </div>
                </form>
            </section>
        </div>
    </main>

    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
