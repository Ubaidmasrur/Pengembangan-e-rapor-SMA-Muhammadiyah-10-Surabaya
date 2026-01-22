<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}" class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Parameter Nilai</li>
            </ol>
        </nav>
    </header> --}}

    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#grade-parameters-table" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to
                table</a>
            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar Parameter Nilai</h1>
                <x-buttons.create :href="route('admin.grade_parameters.create')" label="Tambah Parameter" />
            </header>

            <x-alert.success />

            <x-table.search :action="route('admin.grade_parameters.index')" placeholder="Cari huruf / skor..." aria-label="Pencarian Parameter Nilai" />

            <section>
                <div class="overflow-x-auto">
                    <table id="grade-parameters-table"
                        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                        aria-describedby="grade-parameters-caption">
                        <caption id="grade-parameters-caption" class="text-left text-gray-700 font-medium mb-2">
                            Tabel daftar parameter nilai beserta huruf, skor minimum, maksimum, dan aksi.
                        </caption>
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 border-b">Huruf</th>
                                <th class="px-4 py-2 border-b">Skor Minimum</th>
                                <th class="px-4 py-2 border-b">Skor Maksimum</th>
                                <th class="px-4 py-2 border-b text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gradeParameters as $param)
                                <tr
                                    class="border-b hover:bg-gray-50 @if ($param->trashed()) bg-red-50 text-red-700 @endif">
                                    <td class="px-4 py-2">{{ $param->grade_letter }}</td>
                                    <td class="px-4 py-2">{{ $param->min_score }}</td>
                                    <td class="px-4 py-2">{{ $param->max_score }}</td>
                                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                        @if ($param->trashed())
                                            <x-buttons.restore :action="route('admin.grade_parameters.restore', $param->id)" />
                                            <x-buttons.force-delete :action="route('admin.grade_parameters.forceDelete', $param->id)" />
                                        @else
                                            <x-buttons.edit :href="route('admin.grade_parameters.edit', $param)" />
                                            <x-buttons.delete :action="route('admin.grade_parameters.destroy', $param)" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        {{ request('q') ? 'Tidak ada hasil untuk pencarian "' . request('q') . '"' : 'Belum ada data parameter nilai.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Pagination" class="mb-4 mt-4">
                    {{ $gradeParameters->links('vendor.pagination.default') }}
                </nav>
            </section>
        </div>
    </main>

    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
