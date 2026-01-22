<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}" class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Siswa</li>
            </ol>
        </nav>
    </header> --}}
    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#students-table" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to table</a>
            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar Siswa</h1>
                <x-buttons.create :href="route('admin.students.create')" label="Tambah Siswa" />
            </header>

            <x-alert.success />

            <x-table.search :action="route('admin.students.index')" placeholder="Cari nama / nisn..." aria-label="Pencarian Siswa" />

            <section>
                <div class="overflow-x-auto">
                    <table id="students-table"
                        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                        aria-describedby="students-caption">
                        <caption id="students-caption" class="text-left text-gray-700 font-medium mb-2">
                            Tabel daftar siswa beserta jenis kelamin, tanggal lahir, disabilitas, dan aksi.
                        </caption>
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 border-b">Nama</th>
                                <th class="px-4 py-2 border-b">NISN</th>
                                <th class="px-4 py-2 border-b">Jenis Kelamin</th>
                                <th class="px-4 py-2 border-b">Tanggal Lahir</th>
                                <th class="px-4 py-2 border-b">Disabilitas</th>
                                <th class="px-4 py-2 border-b text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr
                                    class="border-b hover:bg-gray-50 @if ($student->trashed()) bg-red-50 text-red-700 @endif">
                                    <td class="px-4 py-2">{{ $student->name }}</td>
                                    <td class="px-4 py-2">{{ $student->nisn }}</td>
                                    <td class="px-4 py-2">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td class="px-4 py-2">{{ $student->birth_date }}</td>
                                    <td class="px-4 py-2">{{ $student->disability_type ?: '-' }}</td>
                                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                        @if ($student->trashed())
                                            <x-buttons.restore :action="route('admin.students.restore', $student->id)" />
                                            <x-buttons.force-delete :action="route('admin.students.forceDelete', $student->id)" />
                                        @else
                                            <x-buttons.edit :href="route('admin.students.edit', $student)" />
                                            <x-buttons.delete :action="route('admin.students.destroy', $student)" />
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
                    {{ $students->links('vendor.pagination.default') }}
                </nav>
            </section>
        </div>
    </main>
    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
