<x-app-layout>
    <main class="max-w-5xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Input Nilai Siswa</h1>

        <form method="POST" action="/" class="bg-white rounded-2xl shadow-lg p-6 space-y-6">
            @csrf

            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Siswa</label>
                <select id="student_id" name="student_id" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:outline-none" required>
                    <option value="">Pilih Siswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                <select id="subject_id" name="subject_id" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:outline-none" required>
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik</label>
                <select id="academic_year_id" name="academic_year_id" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:outline-none" required>
                    <option value="">Pilih Tahun Akademik</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->year }} - {{ $year->semester }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select id="class_id" name="class_id" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:outline-none" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($schoolClasses as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">Guru</label>
                <select id="teacher_id" name="teacher_id" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:outline-none" required>
                    <option value="">Pilih Guru</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="score" class="block text-sm font-medium text-gray-700 mb-1">Nilai Akhir</label>
                <input type="number" step="0.01" min="0" max="100" id="score" name="score" class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-green-500 focus:outline-none" required>
            </div>
            <div>
                <label for="grade_letter" class="block text-sm font-medium text-gray-700 mb-1">Huruf</label>
                <input type="text" maxlength="2" id="grade_letter" name="grade_letter" class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="motorik" class="block text-sm font-medium text-gray-700 mb-1">Motorik</label>
                <input type="number" step="0.01" min="0" max="100" id="motorik" name="motorik" class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="kognitif" class="block text-sm font-medium text-gray-700 mb-1">Kognitif</label>
                <input type="number" step="0.01" min="0" max="100" id="kognitif" name="kognitif" class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="sosial" class="block text-sm font-medium text-gray-700 mb-1">Sosial</label>
                <input type="number" step="0.01" min="0" max="100" id="sosial" name="sosial" class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea id="notes" name="notes" rows="2" class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-green-500 focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </main>
</x-app-layout>