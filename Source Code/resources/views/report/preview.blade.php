<x-app-layout>
    <main class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Preview Laporan</h2>

                <div class="space-y-2">
                    <div><strong>Nama:</strong> {{ $report->student->name ?? '-' }}</div>
                    <div><strong>NIS:</strong> {{ $report->student->nis ?? '-' }}</div>
                    <div><strong>Kelas:</strong> {{ $report->schoolClass->name ?? '-' }}</div>
                    <div><strong>Tahun Ajaran:</strong> {{ $report->academicYear->name ?? '-' }}</div>
                    <div><strong>Semester:</strong> {{ ucfirst($report->semester ?? '-') }}</div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('admin.report.download', $report->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Download PDF</a>
                    <a href="{{ route('admin.report.admin') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded">Kembali</a>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
