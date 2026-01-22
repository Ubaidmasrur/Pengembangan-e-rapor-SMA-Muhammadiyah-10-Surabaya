<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li><a href="{{ route('admin.students.index') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Siswa</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Edit</li>
            </ol>
        </nav>
    </header> --}}

    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{-- Judul Form --}}
            <h1 class="text-2xl font-bold mb-4" id="form-title">Edit Siswa</h1>

            {{-- Form Edit Siswa --}}
            <form method="POST" action="{{ route('admin.students.update', $student) }}" class="space-y-4"
                aria-labelledby="form-title">
                @csrf
                @method('PUT')

                {{-- Input Nama --}}
                <x-form-input name="name" label="Nama" :value="old('name', $student->name)" />

                {{-- Input NISN --}}
                <x-form-input name="nisn" label="NISN" :value="old('nisn', $student->nisn)" />

                {{-- Select Jenis Kelamin --}}
                <x-form-select name="gender" label="Jenis Kelamin" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :value="old('gender', $student->gender)" />

                {{-- Input Tanggal Lahir --}}
                <x-form-input name="birth_date" label="Tanggal Lahir" type="date" :value="old('birth_date', $student->birth_date?->format('Y-m-d'))" />

                {{-- Input Tipe Disabilitas --}}
                <x-form-input name="disability_type" label="Tipe Disabilitas" :value="old('disability_type', $student->disability_type)" />

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-2 mt-4">
                    <x-buttons.back :href="route('admin.students.index')" />
                    <x-buttons.save label="Perbaharui" />
                </div>
            </form>
        </div>
    </main>

    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
