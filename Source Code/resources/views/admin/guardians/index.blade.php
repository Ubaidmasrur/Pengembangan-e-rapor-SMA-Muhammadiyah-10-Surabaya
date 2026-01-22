<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Wali</li>
            </ol>
        </nav>
    </header> --}}
    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#guardians-table" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to table</a>
            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar Wali</h1>
                <a href="{{ route('admin.guardians.create') }}"
                    class="mt-4 sm:mt-0 inline-block bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 font-semibold transition focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="Tambah Wali Baru">
                    + Tambah Wali
                </a>
            </header>
            @if (session('success'))
                <div class="mb-4">
                    <div
                        class="flex items-center justify-between px-4 py-3 rounded-md bg-green-100 text-green-800 shadow">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()"
                            class="text-green-600 hover:text-green-900 focus:ring focus:ring-green-400"
                            aria-label="Tutup notifikasi sukses">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <section aria-labelledby="search-heading">
                <h2 id="search-heading" class="sr-only">Cari Wali</h2>
                <form method="GET" action="{{ route('admin.guardians.index') }}"
                    class="flex items-center gap-2 w-full sm:w-auto mb-4" role="search" aria-label="Pencarian Wali">
                    <label for="search-q" class="sr-only">Cari nama atau telepon</label>
                    <input type="text" name="q" id="search-q" value="{{ request('q') }}"
                        placeholder="Cari nama atau telepon..."
                        class="px-4 py-2 rounded border border-gray-300 focus:ring focus:ring-blue-200 focus:outline-none w-full sm:w-64"
                        aria-label="Cari nama atau telepon" />
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white font-semibold hover:bg-blue-700 transition flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Cari Wali">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        Cari
                    </button>
                </form>
            </section>
            <section>
                <div class="overflow-x-auto">
                    <table id="guardians-table"
                        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                        aria-describedby="guardians-caption">
                        <caption id="guardians-caption" class="text-left text-gray-700 font-medium mb-2">
                            Tabel daftar wali beserta nama, hubungan, telepon, nama siswa dan aksi.
                        </caption>
                        <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-2 text-left">Nama</th>
                                <th class="px-4 py-2 text-left">Hubungan</th>
                                <th class="px-4 py-2 text-left">Telepon</th>
                                <th class="px-4 py-2 text-left">Siswa</th>
                                <th class="px-4 py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($guardians as $guardian)
                                <tr class="@if ($guardian->trashed()) bg-red-100 @endif">
                                    <td class="px-4 py-3 border-b font-semibold text-left">{{ $guardian->name }}</td>
                                    <td class="px-4 py-3 border-b font-semibold text-left">{{ $guardian->relationship }}
                                    </td>
                                    <td class="px-4 py-3 border-b font-semibold text-left">{{ $guardian->phone ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 border-b font-semibold text-left">
                                        {{ $guardian->student->name }}</td>
                                    <td class="px-4 py-3 align-middle text-right space-x-2 whitespace-nowrap">
                                        @if ($guardian->trashed())
                                            <form action="{{ route('admin.guardians.restore', $guardian->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1 rounded bg-green-100 text-green-700 font-semibold hover:bg-green-200 transition focus:outline-none focus:ring-2 focus:ring-green-400"
                                                    aria-label="Pulihkan wali {{ $guardian->name }}">
                                                    Pulihkan
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.guardians.forceDelete', $guardian->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1 rounded bg-red-100 text-red-700 font-semibold hover:bg-red-200 transition focus:outline-none focus:ring-2 focus:ring-red-400"
                                                    aria-label="Hapus permanen wali {{ $guardian->name }}"
                                                    onclick="return confirm('Hapus permanen?')">
                                                    Hapus Permanen
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.guardians.edit', $guardian) }}"
                                                class="inline-flex items-center px-3 py-1 rounded bg-blue-100 text-blue-700 font-semibold hover:bg-blue-200 transition focus:outline-none focus:ring-2 focus:ring-blue-400"
                                                aria-label="Ubah wali {{ $guardian->name }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536M9 13l6.536-6.536a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13z" />
                                                </svg>
                                                Ubah
                                            </a>
                                            <form action="{{ route('admin.guardians.destroy', $guardian) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1 rounded bg-red-100 text-red-700 font-semibold hover:bg-red-200 transition focus:outline-none focus:ring-2 focus:ring-red-400"
                                                    aria-label="Hapus wali {{ $guardian->name }}"
                                                    onclick="return confirm('Yakin ingin menghapus?')">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 19a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2H8a2 2 0 00-2 2v12zm2-10V5a2 2 0 012-2h4a2 2 0 012 2v4" />
                                                        <line x1="9" y1="10" x2="9"
                                                            y2="16" />
                                                        <line x1="15" y1="10" x2="15"
                                                            y2="16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">
                                        {{ request('q') ? 'Tidak ada hasil untuk pencarian "' . request('q') . '"' : 'Belum ada data wali.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Pagination" class="mb-4 mt-4">
                    {{ $guardians->links('vendor.pagination.default') }}
                </nav>
            </section>
        </div>
    </main>
    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
