<x-app-layout>
    {{-- Page-specific custom styles for relaxing background and card only --}}

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-yellow-100 text-yellow-900 px-3 py-2 rounded">Lewati ke konten</a>
    <main id="main-content" class="min-h-screen flex flex-col items-center justify-start py-8 px-2 sm:px-6 lg:px-8">
        <div class="w-full max-w-4xl card">
            @if (session('error'))
                <div class="mb-4" role="alert">
                    <div class="px-4 py-3 rounded-md bg-red-100 text-red-800 shadow flex items-center">
                        <i class="fas fa-exclamation-circle w-5 h-5 mr-2 text-red-600" aria-hidden="true"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            @if (!empty($errorMessage))
                <div class="mb-4" role="alert">
                    <div class="px-4 py-3 rounded-md bg-red-100 text-red-800 shadow flex items-center">
                        <i class="fas fa-exclamation-circle w-5 h-5 mr-2 text-red-600" aria-hidden="true"></i>
                        <span class="text-sm font-medium">{{ $errorMessage }}</span>
                    </div>
                </div>
            @endif

            {{-- Breadcrumbs --}}
            <div class="mb-4">
                <x-breadcrumbs :items="[['label' => 'Daftar Siswa', 'url' => route('guru.class_member')]]" />
            </div>

            {{-- Text-to-Speech for accessibility --}}
            <div class="mb-4">
                <x-tts />
            </div>

            <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <h1 class="text-2xl font-bold mb-2 sm:mb-0" id="page-title">Daftar Siswa Kelas</h1>
                <form method="GET" action="" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto"
                    role="search" aria-label="Pencarian Siswa">
                    <label for="class_id" class="sr-only">Pilih Kelas</label>
                    <select name="class_id" id="class_id"
                        class="rounded-md border-gray-300 focus:ring focus:ring-blue-400 px-3 py-2"
                        aria-label="Pilih Kelas">
                        <option value="">Semua Kelas</option>
                        @if (!empty($classes))
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name ?? '-' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <label for="academic_year_id" class="sr-only">Pilih Tahun Ajaran</label>
                    <select name="academic_year_id" id="academic_year_id"
                        class="rounded-md border-gray-300 focus:ring focus:ring-blue-400 px-3 py-2"
                        aria-label="Pilih Tahun Ajaran">
                        <option value="">Semua Tahun</option>
                        @if (!empty($academicYears))
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}"
                                    {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->year ?? '-' }} - {{ $year->semester ?? '-' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <label for="student_search" class="sr-only">Cari nama siswa</label>
                    <input type="text" name="student_search" id="student_search"
                        value="{{ request('student_search') }}"
                        class="rounded-md border-gray-300 focus:ring focus:ring-blue-400 px-3 py-2"
                        placeholder="Cari nama siswa..." aria-label="Cari nama siswa">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring focus:ring-blue-400 font-semibold transition"
                        aria-label="Cari siswa">
                        Cari
                    </button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table id="studentlist-table"
                    class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm sm:text-base"
                    aria-describedby="studentlist-caption">
                    <caption id="studentlist-caption" class="text-left text-gray-700 font-medium mb-2">
                        Tabel daftar siswa berdasarkan filter kelas, tahun ajaran, dan nama.
                    </caption>
                    <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-3 border-b font-semibold text-left">Nama</th>
                            <th scope="col" class="px-4 py-3 border-b font-semibold text-left">Kelas</th>
                            <th scope="col" class="px-4 py-3 border-b font-semibold text-left">Tahun Ajaran</th>
                            <th scope="col" class="px-4 py-3 border-b font-semibold text-left">Semester</th>
                            <th scope="col" class="px-4 py-3 border-b font-semibold text-center">
                                Riwayat<br />Penilaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($students) && $students->count())
                            @foreach ($students as $row)
                                <tr class="border-b transition hover:bg-blue-50">
                                    <td class="px-4 py-3">{{ $row->student_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->class_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->academic_year ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->semester ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a class="text-blue-600 hover:underline focus:ring focus:ring-blue-400 mr-3"
                                            href="{{ route('guru.student.history', $row->student_id) }}"
                                            aria-label="Lihat riwayat penilaian {{ $row->student_name ?? '' }}">
                                            <i class="fas fa-history" aria-hidden="true"></i>
                                        </a>  x
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada data siswa
                                    ditemukan.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if (!empty($students))
                <nav aria-label="Pagination" class="mt-6 flex justify-center">
                    {{ $students->links('vendor.pagination.default') }}
                </nav>
            @endif
        </div>
    </main>
    <footer class="sr-only" aria-hidden="true"></footer>
</x-app-layout>
