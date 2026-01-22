<x-app-layout>
    <main>
        <div class="max-w-3xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold mb-4">Tambah Mapping Kelas - Guru - Siswa</h1>

            {{-- Alert sukses standar --}}
            <x-alert.success />

            {{-- Alert error ringkas (opsional, jika mau tetap tampilkan list errors) --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.class_assignments.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Tahun Ajaran --}}
                <x-form-select name="academic_year_id" label="Tahun Ajaran" :options="$academicYears->mapWithKeys(fn($y) => [$y->id => $y->year . ' - Semester ' . $y->semester])" :value="old('academic_year_id')"
                    placeholder="-- Pilih Tahun Ajaran --" />

                {{-- Kelas --}}
                <x-form-select name="class_id" label="Kelas" :options="$classes->pluck('name', 'id')" :value="old('class_id')"
                    placeholder="-- Pilih Kelas --" />

                {{-- Guru --}}
                <x-form-select name="teacher_id" label="Guru" :options="$teachers->pluck('name', 'id')" :value="old('teacher_id')"
                    placeholder="-- Pilih Guru --" />

                {{-- Wali --}}
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_wali" value="1" class="rounded mr-2"
                            {{ old('is_wali') ? 'checked' : '' }}>
                        Jadikan sebagai Wali Kelas
                    </label>
                    @error('is_wali')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Siswa --}}
                <x-student-selector :students="$students" :selected="old('students', [])" />

                {{-- Aksi --}}
                <div class="flex justify-end gap-2">
                    <x-buttons.back :href="route('admin.class_assignments.index')" label="Batal" />
                    <x-buttons.save label="Simpan" />
                </div>
            </form>
        </div>
    </main>
</x-app-layout>
