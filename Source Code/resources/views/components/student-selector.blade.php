@props([
    'students' => [],
    'selected' => [],
])

@php
    $selectedData = collect($students)
        ->whereIn('id', $selected)
        ->map(
            fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'nisn' => $s->nisn,
            ],
        )
        ->values();

    $studentsGeneralErrors = $errors->get('students'); // error key "students"
    $studentsItemErrors = $errors->get('students.*'); // error key "students.*"
@endphp

<div x-data="studentSelector()" class="space-y-2">
    <div class="flex items-center justify-between">
        <label class="block font-semibold mb-1">Siswa yang Dipilih</label>
        <button type="button" @click="openModal"
            class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
            + Tambah Siswa
        </button>
    </div>

    {{-- Error dari backend --}}
    @if (!empty($studentsGeneralErrors) || !empty($studentsItemErrors))
        <div x-data="{ show: true }" x-show="show"
            class="relative rounded border border-red-200 bg-red-50 text-red-700 p-2 text-sm" role="alert"
            aria-live="polite">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($studentsGeneralErrors as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
                @foreach ($studentsItemErrors as $messages)
                    @foreach ($messages as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                @endforeach
            </ul>
            <button type="button" @click="show=false" class="absolute top-1 right-1 text-red-600 hover:text-red-800">
                ✕
            </button>
        </div>
    @endif

    {{-- Error lokal (duplikat) --}}
    <template x-if="localError">
        <div x-data="{ show: true }" x-show="show"
            class="relative rounded border border-red-200 bg-red-50 text-red-700 p-2 text-sm" role="alert"
            aria-live="polite">
            <span x-text="localError"></span>
            <button type="button" @click="show=false" class="absolute top-1 right-1 text-red-600 hover:text-red-800">
                ✕
            </button>
        </div>
    </template>

    {{-- List siswa yang dipilih --}}
    <template x-for="(student, index) in selectedStudents" :key="student.id">
        <div class="flex justify-between items-center bg-gray-100 p-2 rounded">
            <span class="text-sm" x-text="student.name + ' (NISN: ' + student.nisn + ')'"></span>
            <div class="flex items-center gap-3">
                <button type="button" @click="removeStudent(index)"
                    class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
            </div>
            <input type="hidden" name="students[]" :value="student.id">
        </div>
    </template>

    {{-- Modal Pilih Siswa --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-4 space-y-4">
            <h2 class="text-lg font-semibold">Pilih Siswa</h2>
            <div class="max-h-64 overflow-y-auto border rounded">
                @foreach ($students as $student)
                    <div class="px-3 py-2 border-b flex justify-between items-center">
                        <span>{{ $student->name }}
                            <span class="text-xs text-gray-500">({{ $student->nisn }})</span>
                        </span>
                        <button type="button"
                            @click="addStudent({ id: {{ $student->id }}, name: '{{ $student->name }}', nisn: '{{ $student->nisn }}' })"
                            class="text-blue-600 hover:underline text-sm">
                            Tambah
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="text-right">
                <button type="button" @click="closeModal"
                    class="text-gray-600 hover:text-gray-800 text-sm">Tutup</button>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function studentSelector() {
                return {
                    selectedStudents: @json($selectedData),
                    showModal: false,
                    localError: '',

                    openModal() {
                        this.localError = '';
                        this.showModal = true;
                    },
                    closeModal() {
                        this.showModal = false;
                    },

                    addStudent(student) {
                        this.localError = '';
                        if (this.selectedStudents.some(s => s.id === student.id)) {
                            this.localError = 'Siswa sudah ada di daftar pilihan.';
                            return;
                        }
                        this.selectedStudents.push(student);
                    },

                    removeStudent(index) {
                        this.localError = '';
                        this.selectedStudents.splice(index, 1);
                    }
                }
            }
        </script>
    @endpush
@endonce
