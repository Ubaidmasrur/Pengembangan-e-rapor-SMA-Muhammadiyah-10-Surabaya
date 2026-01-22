<x-app-layout>
    {{-- Breadcrumb --}}
    {{--
    <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}" class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">User</li>
            </ol>
        </nav>
    </header>
    --}}

    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#users-table" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to table</a>

            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar User</h1>
                <x-buttons.create :href="route('admin.users.create')" label="Tambah User" />
            </header>

            <x-alert.success />

            <x-table.search :action="route('admin.users.index')" placeholder="Cari nama / email / role..." aria-label="Pencarian User" />

            <section>
                <div class="overflow-x-auto">
                    <table id="users-table"
                        class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                        aria-describedby="users-caption">
                        <caption id="users-caption" class="text-left text-gray-700 font-medium mb-2">
                            Tabel daftar user beserta role dan aksi.
                        </caption>
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 border-b">Nama</th>
                                <th class="px-4 py-2 border-b">Email</th>
                                <th class="px-4 py-2 border-b">Role</th>
                                <th class="px-4 py-2 border-b text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr
                                    class="border-b hover:bg-gray-50 @if ($user->trashed()) bg-red-50 text-red-700 @endif">
                                    <td class="px-4 py-2">{{ $user->name }}</td>
                                    <td class="px-4 py-2">{{ $user->email }}</td>
                                    <td class="px-4 py-2">{{ ucfirst($user->role) }}</td>
                                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                        @if ($user->trashed())
                                            <x-buttons.restore :action="route('admin.users.restore', $user->id)" />
                                            <x-buttons.force-delete :action="route('admin.users.forceDelete', $user->id)" />
                                        @else
                                            <x-buttons.edit :href="route('admin.users.edit', $user)" />
                                            <x-buttons.delete :action="route('admin.users.destroy', $user)" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        {{ request('q') ? 'Tidak ada hasil untuk pencarian "' . request('q') . '"' : 'Belum ada data user.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Pagination" class="mt-4">
                    {{ $users->links('vendor.pagination.default') }}
                </nav>
            </section>
        </div>
    </main>

    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
