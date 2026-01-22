<x-app-layout>
    <main class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <header class="mb-6">
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Hasil Belajar Saya</h1>
            </header>

            <section class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" action="{{ route('siswa.reports.index') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="text-sm text-gray-600">Tahun Ajaran</label>
                        <select name="academic_year" class="mt-1 block w-full rounded border-gray-200">
                            <option value="">Semua</option>
                            @foreach ($academicYears ?? [] as $ay)
                                <option value="{{ $ay->id }}"
                                    {{ (string) request('academic_year') === (string) $ay->id ? 'selected' : '' }}>
                                    {{ $ay->year }} - {{ $ay->semester }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Cari</button>
                        <a href="{{ route('siswa.laporan.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Reset</a>
                    </div>
                </form>
            </section>

            <section class="bg-white rounded-lg shadow p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="text-left text-sm text-gray-600">
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">Kelas</th>
                                <th class="px-4 py-2">Tahun Ajaran</th>
                                <th class="px-4 py-2">Semester</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($reports ?? [] as $index => $report)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $reports->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $report->class_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $report->academic_year ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $report->semester ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            @if (!empty($report->has_report))
                                                <a href="{{ route('siswa.reports.preview', $report->sample_id) }}"
                                                    target="_blank"
                                                    class="px-3 py-1 bg-green-600 text-white rounded text-xs">Lihat
                                                    Rapor</a>
                                                <a href="{{ route('siswa.reports.export', $report->sample_id) }}"
                                                    class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Export
                                                    Rapor</a>
                                            @else
                                                <button type="button" disabled
                                                    class="px-3 py-1 bg-gray-300 text-gray-600 rounded text-xs">No
                                                    Report</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada
                                        laporan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    @if (method_exists($reports, 'links'))
                        {{ $reports->appends(request()->query())->links() }}
                    @endif
                </div>
            </section>
        </div>
    </main>
</x-app-layout>
