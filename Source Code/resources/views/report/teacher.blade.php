<x-app-layout>
    <main class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <header class="mb-6">
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Laporan Hasil Belajar (Guru)</h1>
            </header>

            <section class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" action="{{ route('guru.report.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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

                    <div>
                        <label class="text-sm text-gray-600">Semester</label>
                        <select name="semester" class="mt-1 block w-full rounded border-gray-200">
                            <option value="">Semua</option>
                            <option value="Ganjil" {{ request('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil
                            </option>
                            <option value="Genap" {{ request('semester') === 'Genap' ? 'selected' : '' }}>Genap
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Kelas</label>
                        <select name="class_id" class="mt-1 block w-full rounded border-gray-200">
                            <option value="">Semua</option>
                            @foreach ($classes ?? [] as $class)
                                <option value="{{ $class->id }}"
                                    {{ (string) request('class_id') === (string) $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Cari</button>
                        <a href="{{ route('guru.report.index') }}"
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
                                <th class="px-4 py-2">Nama Siswa</th>
                                <th class="px-4 py-2">NIS</th>
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
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $report->student_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $report->nis ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $report->class_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $report->academic_year ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $report->semester ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="flex flex-col gap-2">
                                            <div class="flex flex-wrap gap-2">
                                                @if (!empty($report->has_report))
                                                    <a href="{{ route('guru.report.preview', $report->sample_id) }}"
                                                        target="_blank"
                                                        class="px-3 py-1 bg-green-600 text-white rounded text-xs">
                                                        Lihat Rapor
                                                    </a>
                                                    <a href="{{ route('guru.report.export', $report->sample_id) }}"
                                                        target="_blank"
                                                        class="px-3 py-1 bg-purple-600 text-white rounded text-xs">
                                                        Export Rapor
                                                    </a>
                                                    <button type="button"
                                                        class="text-blue-600 hover:underline text-xs text-left"
                                                        onclick="document.getElementById('collapse-{{ $index }}').classList.toggle('hidden')">
                                                        📂 Lihat Bulanan
                                                    </button>
                                                @else
                                                    <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded text-xs">
                                                        No Report
                                                    </span>
                                                @endif
                                            </div>

                                        </div>
                                    </td>
                                </tr>

                                {{-- Collapse Row --}}
                                <tr id="collapse-{{ $index }}" class="hidden bg-gray-50">
                                    <td colspan="7" class="px-4 py-3">
                                        @if (!empty($report->periods) && count($report->periods))
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                @foreach ($report->periods as $period)
                                                    <a href="{{ route('guru.report.preview.month', ['period' => $period, 'student_id' => $report->student_id]) }}"
                                                        target="_blank"
                                                        class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded hover:bg-green-200">
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->isoFormat('MMMM YYYY') }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">Tidak ada data bulanan tersedia.</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                        Data tidak ditemukan.
                                    </td>
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

    <script>
        function toggleCollapse(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.toggle('hidden');
            }
        }
    </script>
</x-app-layout>
