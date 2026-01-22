<x-app-layout>
    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#academic-years-table" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to
                table</a>

            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar Tahun Pelajaran</h1>
                <x-buttons.create :href="route('admin.academic_years.create')">+ Tambah Tahun Ajaran</x-buttons.create>
            </header>

            <x-alert.success />

            {{-- Pencarian --}}
            <x-table.search :action="route('admin.academic_years.index')" placeholder="Cari nama atau semester..." />

            {{-- Tabel --}}
            <section>
                <div class="overflow-x-auto">
                    <table id="academic-years-table"
                        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                        aria-describedby="academic-years-caption">
                        <caption id="academic-years-caption" class="text-left text-gray-700 font-medium mb-2">
                            Tabel daftar tahun pelajaran beserta semester, status aktif, dan aksi.
                        </caption>
                        <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 border-b font-semibold text-left">Tahun</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Semester</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Bulan Mulai</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Bulan Akhir</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Status Aktif</th>
                                <th class="px-4 py-3 border-b font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($academicYears as $year)
                                <tr
                                    class="border-b transition hover:bg-gray-100 @if ($year->trashed()) bg-red-50 text-red-700 @endif">
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">{{ $year->year }}</td>
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">{{ $year->semester }}</td>
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">
                                        {{ $year->start_month ? \Carbon\Carbon::parse($year->start_month)->translatedFormat('F Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">
                                        {{ $year->end_month ? \Carbon\Carbon::parse($year->end_month)->translatedFormat('F Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">
                                        @if ($year->is_active)
                                            <span class="text-green-600 font-medium">Aktif</span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle text-right space-x-2 whitespace-nowrap">
                                        @if ($year->trashed())
                                            <x-buttons.restore :action="route('admin.academic_years.restore', $year->id)" />
                                            <x-buttons.force-delete :action="route('admin.academic_years.forceDelete', $year->id)" />
                                        @else
                                            <x-buttons.edit :href="route('admin.academic_years.edit', $year)" />
                                            <x-buttons.delete :action="route('admin.academic_years.destroy', $year)" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">
                                        {{ request('q') ? 'Tidak ada hasil untuk pencarian "' . request('q') . '"' : 'Belum ada data tahun ajaran.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Pagination" class="mb-4 mt-4">
                    {{ $academicYears->links('vendor.pagination.default') }}
                </nav>
            </section>
        </div>
    </main>

    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
