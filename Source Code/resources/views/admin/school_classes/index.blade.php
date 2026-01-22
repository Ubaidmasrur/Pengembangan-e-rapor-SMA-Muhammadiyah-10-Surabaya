<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Kelas</li>
            </ol>
        </nav>
    </header> --}}
    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#school-class-table" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to
                table</a>

            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar Kelas</h1>

                {{-- Create button pakai komponen agar konsisten --}}
                <x-buttons.create :href="route('admin.school_classes.create')" label="Tambah Kelas" />
            </header>

            {{-- Alert sukses standar --}}
            <x-alert.success />

            {{-- Search komponen standar --}}
            <section aria-labelledby="search-heading">
                <h2 id="search-heading" class="sr-only">Cari Kelas</h2>
                <x-table.search :action="route('admin.school_classes.index')" placeholder="Cari nama..." aria-label="Pencarian Kelas" />
            </section>

            <section>
                <div class="overflow-x-auto">
                    <table id="school-class-table"
                        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                        aria-describedby="school-class-caption">
                        <caption id="school-class-caption" class="text-left text-gray-700 font-medium mb-2">
                            Tabel daftar kelas beserta aksi.
                        </caption>
                        <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 border-b font-semibold text-left">Sekolah</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Nama Kelas</th>
                                <th class="px-4 py-3 border-b font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($school_classes as $class)
                                <tr
                                    class="border-b transition hover:bg-gray-100 @if ($class->trashed()) bg-red-50 text-red-700 @endif">
                                    <td class="px-4 py-2">
                                        {{ $class->school?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $class->name }}
                                        @if ($class->trashed())
                                            <span
                                                class="ml-2 inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded bg-red-100 text-red-700 align-middle">
                                                Terhapus
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle text-right space-x-2 whitespace-nowrap">
                                        @if ($class->trashed())
                                            {{-- Restore & Force Delete pakai komponen --}}
                                            <x-buttons.restore :action="route('admin.school_classes.restore', $class->id)" />
                                            <x-buttons.force-delete :action="route('admin.school_classes.forceDelete', $class->id)" />
                                        @else
                                            {{-- Edit & Soft Delete pakai komponen --}}
                                            <x-buttons.edit :href="route('admin.school_classes.edit', $class)" />
                                            <x-buttons.delete :action="route('admin.school_classes.destroy', $class)" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-gray-500">
                                        {{ request('q') ? 'Tidak ada hasil untuk pencarian "' . request('q') . '"' : 'Belum ada data kelas.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination konsisten dengan template lain --}}
                <nav aria-label="Pagination" class="mb-4 mt-4">
                    {{ $school_classes->links('vendor.pagination.default') }}
                </nav>
            </section>
        </div>
    </main>
    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
