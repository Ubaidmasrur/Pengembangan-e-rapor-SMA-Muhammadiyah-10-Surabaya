<x-app-layout>
    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- Filter Form (tetap) --}}
            <form method="GET" action="{{ route('admin.class_assignments.index') }}"
                class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label for="q" class="block text-sm font-medium text-gray-700">Guru/Kelas</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="Contoh: IPA 1, Budi">
                </div>

                <div>
                    <label for="student" class="block text-sm font-medium text-gray-700">Nama Siswa</label>
                    <input type="text" name="student" id="student" value="{{ request('student') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="Contoh: John Doe">
                </div>

                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">Tahun Ajaran &
                        Semester</label>
                    <select name="year" id="year"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Semua --</option>
                        @foreach ($academicYears as $year)
                            @php $value = $year->year.'-'.$year->semester; @endphp
                            <option value="{{ $value }}" {{ request('year') === $value ? 'selected' : '' }}>
                                {{ $year->year }} - Semester {{ $year->semester }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="class" class="block text-sm font-medium text-gray-700">Kelas</label>
                    <select name="class" id="class"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Semua --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->name }}"
                                {{ request('class') === $class->name ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-4 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cari
                    </button>
                </div>
            </form>

            <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-800" id="page-title">Daftar Mapping Kelas - Guru - Siswa</h1>

                {{-- Create pakai komponen --}}
                <x-buttons.create :href="route('admin.class_assignments.create')" label="Tambah Mapping" />
            </header>

            {{-- Alert sukses konsisten --}}
            <x-alert.success />

            <section>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base">
                        <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 border-b font-semibold text-left">#</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Tahun Ajaran</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Kelas</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Guru</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Jumlah Siswa</th>
                                <th class="px-4 py-3 border-b font-semibold text-left">Wali</th>
                                <th class="px-4 py-3 border-b font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                                <tr
                                    class="border-b transition hover:bg-gray-100 @if (method_exists($assignment, 'trashed') && $assignment->trashed()) bg-red-50 text-red-700 @endif">
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        {{ $assignment->academicYear->year }} - Semester
                                        {{ $assignment->academicYear->semester }}
                                    </td>
                                    <td class="px-4 py-3">{{ $assignment->class->name }}</td>
                                    <td class="px-4 py-3">{{ $assignment->teacher->name }}</td>
                                    <td class="px-4 py-3">{{ $assignment->students_count }}</td>
                                    <td class="px-4 py-3">
                                        @if ($assignment->is_wali)
                                            <span class="text-green-600 font-semibold">Ya</span>
                                        @else
                                            <span class="text-gray-500">Tidak</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 align-middle text-right space-x-2 whitespace-nowrap">
                                        @if (method_exists($assignment, 'trashed') && $assignment->trashed())
                                            {{-- Restore & Force Delete --}}
                                            <x-buttons.restore :action="route('admin.class_assignments.restore', $assignment->id)" />
                                            <x-buttons.force-delete :action="route('admin.class_assignments.forceDelete', $assignment->id)" />
                                        @else
                                            {{-- Edit & Soft Delete --}}
                                            <x-buttons.edit :href="route('admin.class_assignments.edit', $assignment->id)" />
                                            <x-buttons.delete :action="route('admin.class_assignments.destroy', $assignment->id)" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-gray-500">
                                        Belum ada data mapping.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- (Opsional) Pagination, jika $assignments adalah paginator --}}
                @if (method_exists($assignments, 'links'))
                    <nav aria-label="Pagination" class="mb-4 mt-4">
                        {{ $assignments->links('vendor.pagination.default') }}
                    </nav>
                @endif
            </section>
        </div>
    </main>
</x-app-layout>
