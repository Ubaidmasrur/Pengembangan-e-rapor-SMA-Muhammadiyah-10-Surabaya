<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Sekolah</li>
            </ol>
        </nav>
    </header> --}}
    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#schools-table" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to table</a>

            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar Sekolah</h1>

                {{-- Create button pakai komponen agar konsisten --}}
                <x-buttons.create :href="route('admin.schools.create')" label="Tambah Sekolah" />
            </header>

            {{-- Alert sukses standar --}}
            <x-alert.success />

            {{-- Search komponen standar --}}
            <section aria-labelledby="search-heading">
                <h2 id="search-heading" class="sr-only">Cari Sekolah</h2>
                <x-table.search :action="route('admin.schools.index')" placeholder="Cari nama atau email..."
                    aria-label="Pencarian Sekolah" />
            </section>

            <section>
                <div class="overflow-x-auto">
                    <table id="schools-table"
                        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                        aria-describedby="schools-caption">
                        <caption id="schools-caption" class="text-left text-gray-700 font-medium mb-2">
                            Tabel daftar sekolah beserta alamat, telepon, email, kepala sekolah, dan aksi.
                        </caption>
                        <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 border-b font-semibold text-left">Nama Sekolah</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Alamat</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Telepon</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Email</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Kepala Sekolah</th>
                                <th class="px-4 py-3 border-b font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($schools as $school)
                                <tr
                                    class="border-b transition hover:bg-gray-100 @if ($school->trashed()) bg-red-50 text-red-700 @endif">
                                    <td class="px-4 py-2">{{ $school->name }}</td>
                                    <td class="px-4 py-2">{{ $school->address }}</td>
                                    <td class="px-4 py-2">{{ $school->phone }}</td>
                                    <td class="px-4 py-2">{{ $school->email }}</td>
                                    <td class="px-4 py-2">{{ $school->principal_name }}</td>
                                    <td class="px-4 py-3 align-middle text-right space-x-2 whitespace-nowrap">
                                        @if ($school->trashed())
                                            {{-- Restore & Force Delete pakai komponen --}}
                                            <x-buttons.restore :action="route('admin.schools.restore', $school->id)" />
                                            <x-buttons.force-delete :action="route('admin.schools.forceDelete', $school->id)" />
                                        @else
                                            {{-- Edit & Soft Delete pakai komponen --}}
                                            <x-buttons.edit :href="route('admin.schools.edit', $school)" />
                                            <x-buttons.delete :action="route('admin.schools.destroy', $school)" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">
                                        {{ request('q') ? 'Tidak ada hasil untuk pencarian "' . request('q') . '"' : 'Belum ada data sekolah.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination konsisten --}}
                <nav aria-label="Pagination" class="mb-4 mt-4">
                    {{ $schools->links('vendor.pagination.default') }}
                </nav>
            </section>
        </div>
    </main>
    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
