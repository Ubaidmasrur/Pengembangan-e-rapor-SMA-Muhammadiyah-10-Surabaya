<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Kegiatan</li>
            </ol>
        </nav>
    </header> --}}
    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#activities-table" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to table</a>

            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar Kegiatan</h1>
                <x-buttons.create :href="route('admin.activities.create')">+ Tambah Kegiatan</x-buttons.create>
            </header>

            {{-- Alert sukses standar --}}
            <x-alert.success />

            {{-- Pencarian standar --}}
            <section aria-labelledby="search-heading">
                <h2 id="search-heading" class="sr-only">Cari Kegiatan</h2>
                <x-table.search :action="route('admin.activities.index')" placeholder="Cari nama atau tipe..." aria-label="Pencarian Kegiatan" />
            </section>

            <section>
                <div class="overflow-x-auto">
                    <table id="activities-table"
                        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                        aria-describedby="activities-caption">
                        <caption id="activities-caption" class="text-left text-gray-700 font-medium mb-2">
                            Tabel daftar kegiatan beserta tanggal, judul, deskripsi, thumbnail, dan aksi.
                        </caption>
                        <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 border-b font-semibold text-left">Tanggal</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Judul</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Deskripsi</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Thumbnail</th>
                                <th class="px-4 py-3 border-b font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr
                                    class="border-b transition hover:bg-gray-100 @if ($activity->trashed()) bg-red-50 text-red-700 @endif">
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">{{ $activity->activity_date }}
                                    </td>
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">{{ $activity->title }}</td>
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">{{ $activity->description }}
                                    </td>
                                    <td class="px-4 py-3 align-middle whitespace-nowrap">
                                        @if ($activity->thumbnail)
                                            <img src="{{ asset('storage/' . $activity->thumbnail) }}" alt="Thumbnail"
                                                class="h-12 w-12 rounded object-cover">
                                        @else
                                            <span class="text-gray-400 italic">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle text-right space-x-2 whitespace-nowrap">
                                        @if ($activity->trashed())
                                            <x-buttons.restore :action="route('admin.activities.restore', $activity->id)" />
                                            <x-buttons.force-delete :action="route('admin.activities.forceDelete', $activity->id)" />
                                        @else
                                            <x-buttons.edit :href="route('admin.activities.edit', $activity)" />
                                            <x-buttons.delete :action="route('admin.activities.destroy', $activity)" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">
                                        {{ request('q') ? 'Tidak ada hasil untuk pencarian "' . request('q') . '"' : 'Belum ada data kegiatan.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($activities, 'links'))
                    <nav aria-label="Pagination" class="mb-4 mt-4">
                        {{ $activities->links('vendor.pagination.default') }}
                    </nav>
                @endif
            </section>
        </div>
    </main>

    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
