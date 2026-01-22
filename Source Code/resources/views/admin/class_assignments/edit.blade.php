<x-app-layout>
    <main>
        <div class="max-w-3xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold mb-4">Edit Mapping Kelas - Guru - Siswa</h1>

            <form action="{{ route('admin.class_assignments.update', $assignment->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Tahun Ajaran (readonly) --}}
                <div>
                    <label class="block font-semibold mb-1">Tahun Ajaran</label>
                    <input type="hidden" name="academic_year_id" value="{{ $assignment->academic_year_id }}">
                    <input type="text"
                        value="{{ $assignment->academicYear->year }} - Semester {{ $assignment->academicYear->semester }}"
                        class="w-full rounded border-gray-300 bg-gray-100 cursor-not-allowed" readonly>
                </div>

                {{-- Kelas --}}
                <x-form-select name="class_id" label="Kelas" :options="$classes->pluck('name', 'id')" :value="old('class_id', $assignment->class_id)" />

                {{-- Guru --}}
                <x-form-select name="teacher_id" label="Guru" :options="$teachers->pluck('name', 'id')" :value="old('teacher_id', $assignment->teacher_id)" />

                {{-- Wali Kelas --}}
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_wali" value="1" class="rounded mr-2"
                            {{ old('is_wali', $assignment->is_wali) ? 'checked' : '' }}>
                        Jadikan sebagai Wali Kelas
                    </label>
                    @error('is_wali')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilih Siswa --}}
                <x-student-selector :students="$students" :selected="$assignedStudentIds" />

                {{-- Aksi --}}
                <div class="flex justify-end gap-2">
                    <x-buttons.back :href="route('admin.class_assignments.index')" label="Batal" />
                    <x-buttons.save label="Perbarui" />
                </div>
            </form>
        </div>
    </main>
</x-app-layout>
