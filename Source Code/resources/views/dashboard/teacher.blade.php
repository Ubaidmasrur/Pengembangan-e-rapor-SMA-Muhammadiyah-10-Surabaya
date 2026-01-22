<x-app-layout>
    <main aria-labelledby="dashboard-title" role="main" tabindex="-1">
        <div class="py-8 bg-gray-100 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistic Cards -->
                <section class="mb-8">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-400">
                                <!-- Icon placeholder -->
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800">Total Siswa</div>
                <div class="text-lg font-bold text-gray-700">{{ $totalStudents ?? 0 }}</div>
                            </div>
                        </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-400">
                                <!-- Icon placeholder -->
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800">Laporan Selesai</div>
                <div class="text-lg font-bold text-gray-700">{{ $reportsCompleted ?? 0 }}</div>
                            </div>
                        </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-400">
                                <!-- Icon placeholder -->
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800">Menunggu Evaluasi</div>
                <div class="text-lg font-bold text-gray-700">{{ $reportsPending ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Daftar Siswa -->
                <section class="mb-8">
                    <h2 class="font-semibold text-lg text-gray-800 mb-4">Daftar Siswa</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($students as $s)
                        <div class="bg-white rounded-xl shadow p-6 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-400">{{ strtoupper(substr($s->name,0,1)) }}</span>
                                <span class="font-semibold text-gray-800">{{ $s->name }}</span>
                            </div>
                            <div class="space-y-2 mt-2">
                                <div class="h-2 rounded bg-blue-300" style="width: {{ ($studentProgress[$s->id] ?? 0) }}%"></div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <a href="#" class="px-4 py-2 rounded bg-blue-50 text-blue-700 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-400">Lihat</a>
                                <a href="#" class="px-4 py-2 rounded bg-green-50 text-green-700 font-semibold focus:outline-none focus:ring-2 focus:ring-green-400">Nilai</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>

                <!-- Riwayat Penilaian (Top 10) -->
                <section class="mb-8">
                    <h2 class="font-semibold text-lg text-gray-800 mb-4">Riwayat Penilaian</h2>
                    <div class="bg-white rounded-xl shadow p-6">
                        <table class="min-w-full text-sm text-gray-700">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4 text-left font-semibold">Nama Siswa</th>
                                    <th class="py-3 px-4 text-left font-semibold">Tanggal</th>
                                    <th class="py-3 px-4 text-left font-semibold">Mata Pelajaran</th>
                                    <th class="py-3 px-4 text-left font-semibold">Nilai</th>
                                    <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentReports as $r)
                                <tr class="border-b">
                                    <td class="py-2 px-4">{{ $r->student_name }}</td>
                                    <td class="py-2 px-4">{{ \Carbon\Carbon::parse($r->created_at)->format('Y-m-d') }}</td>
                                    <td class="py-2 px-4">{{ $r->subject_name }}</td>
                                    <td class="py-2 px-4">{{ $r->score ?? '-' }}</td>
                                    <td class="py-2 px-4">
                                        <a href="{{ route('guru.history.grades', ['student' => $r->student_id]) }}?open={{ $r->student_grade_id }}" class="text-sm text-teal-700">Lihat</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>
</x-app-layout>
