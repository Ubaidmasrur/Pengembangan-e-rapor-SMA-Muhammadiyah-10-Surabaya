<x-app-layout>
@php
    // All data expected from controller: $student, $activities, $recentReports, $master,
    // $kognitif, $motorik, $sosial, $hasProgress, $summary
    $student = $student ?? null;
    $activities = $activities ?? collect();
    $recentReports = $recentReports ?? collect();
    $master = $master ?? null;
    $kognitif = $kognitif ?? null;
    $motorik = $motorik ?? null;
    $sosial = $sosial ?? null;
    $hasProgress = $hasProgress ?? false;
    $summary = $summary ?? null;
@endphp
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-yellow-100 text-yellow-900 px-3 py-2 rounded">Lewati ke konten</a>
    <main id="main-content" aria-labelledby="dashboard-title" role="main" tabindex="-1">
        <h1 id="dashboard-title" class="sr-only">Dashboard Siswa</h1>
        <div class="py-6 bg-gray-100 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{-- Text-to-Speech for accessibility --}}
                <div class="mb-4">
                    <x-tts />
                </div>
                <!-- Profile & Progress Summary -->
                <section class="mb-8">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Profile Card -->
                        @php
                            // Build a concise accessible narrative for the profile card. If the controller
                            // provides a $profileNarrative it will be used; otherwise construct from available values.
                            $profileNarrative = $profileNarrative ?? (
                                optional($student)->name ? (optional($student)->name . '. ') : ''
                            );
                            if (empty($profileNarrative)) {
                                $profileNarrative = (optional($student)->name ?? 'Nama siswa tidak tersedia') . '. ';
                            }
                            $profileNarrative .= ($className ?? (optional($student)->class_name ?? 'Kelas tidak tersedia')) . '. ';
                            $profileNarrative .= ($academicYearLabel ?? '') ? ($academicYearLabel . '. ') : '';
                            $profileNarrative .= 'Guru pendamping: ' . ($guardianName ?? optional($student)->guardian_name ?? 'tidak tersedia') . '. ';
                            $profileNarrative .= optional($student)->updated_at ? ('Terakhir diperbarui pada ' . \Carbon\Carbon::parse($student->updated_at)->format('d M Y') . '.') : '';
                        @endphp

                        <div
                            class="bg-teal-700 rounded-xl shadow p-6 flex flex-col justify-between text-white md:w-1/3 w-full"
                            role="region" aria-labelledby="student-name" aria-describedby="student-profile-desc">
                            {{-- Hidden narrative for screen readers and TTS to prefer --}}
                            <div id="student-profile-desc" class="sr-only">{{ $profileNarrative }}</div>
                            {{-- Also expose a TTS-friendly element that the TTS widget can opt to read first --}}
                            <div id="tts-profile-text" class="sr-only" data-tts-default="true">{{ $profileNarrative }}</div>
                            {{-- Additional hidden TTS sections: progress, reports, activities --}}
                            @php
                                // prepare progress summary
                                $kogn = $hasProgress ? ($kognitif . '%') : 'tidak tersedia';
                                $mot = $hasProgress ? ($motorik . '%') : 'tidak tersedia';
                                $soc = $hasProgress ? ($sosial . '%') : 'tidak tersedia';
                                $progressNarrative = "Ringkasan perkembangan terbaru. Kognitif: $kogn. Motorik: $mot. Sosial: $soc.";

                                // prepare latest reports for TTS (up to 3)
                                $ttsOwnerId = isset($student) && $student ? ($student->id ?? null) : (auth()->check() ? (auth()->user()->student_id ?? null) : null);
                                $ttsLatestReports = collect($recentReports ?? collect());
                                if ($ttsOwnerId) { $ttsLatestReports = $ttsLatestReports->where('student_id', $ttsOwnerId); }
                                $ttsLatestReports = $ttsLatestReports->sortByDesc('created_at')->values()->take(3);
                                if ($ttsLatestReports->count()) {
                                    $reportLines = [];
                                    foreach ($ttsLatestReports as $r) {
                                        $label = $r->month_label ?? ($r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('F Y') : 'Laporan');
                                        $notes = trim(strip_tags($r->notes ?? 'Ringkasan penilaian'));
                                        $reportLines[] = "$label: $notes";
                                    }
                                    $reportsNarrative = 'Laporan terbaru: ' . implode('; ', $reportLines) . '.';
                                } else {
                                    $reportsNarrative = 'Belum ada laporan penilaian.';
                                }

                                // prepare activities summary
                                $actCount = $activities ? $activities->count() : 0;
                                if ($actCount > 0) {
                                    $nextAct = $activities->sortBy('activity_date')->first();
                                    $actDate = \Carbon\Carbon::parse($nextAct->activity_date)->translatedFormat('d F Y');
                                    $activitiesNarrative = "Terdapat $actCount kegiatan mendatang. Kegiatan berikutnya: {$nextAct->title} pada $actDate.";
                                } else {
                                    $activitiesNarrative = 'Tidak ada kegiatan mendatang.';
                                }
                            @endphp
                            <div id="tts-progress-text" class="sr-only" data-tts-section="progress">{{ $progressNarrative }}</div>
                            <div id="tts-reports-text" class="sr-only" data-tts-section="reports">{{ $reportsNarrative }}</div>
                            <div id="tts-activities-text" class="sr-only" data-tts-section="activities">{{ $activitiesNarrative }}</div>
                            <div class="flex items-center gap-4 mb-4">
                                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white text-teal-700 text-2xl font-bold">{{ strtoupper(substr(optional($student)->name ?? 'R', 0, 1)) }}</span>
                                    <div>
                                        <div id="student-name" class="font-bold text-2xl">{{ optional($student)->name ?? 'Nama Siswa' }}</div>
                                        <div class="text-base font-semibold" id="student-class">{{ $className ?? (optional($student)->class_name ?? '—') }} • {{ $academicYearLabel ?? '' }}</div>
                                    </div>
                                </div>
                            <div class="mb-2">
                                <div class="text-sm font-semibold">Kategori Kebutuhan Khusus</div>
                                <div class="text-lg font-bold text-teal-200">{{ optional($student)->special_needs ?? '—' }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="text-sm font-semibold">Guru Pendamping</div>
                                <div class="text-base font-bold text-teal-200">{{ $guardianName ?? optional($student)->guardian_name ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold">Terakhir Diperbarui</div>
                                <div class="text-base font-bold text-teal-200">{{ optional($student)->updated_at ? \Carbon\Carbon::parse($student->updated_at)->format('d M Y') : '' }}</div>
                            </div>
                        </div>
                        <!-- Progress Summary -->
                        <div class="bg-white rounded-xl shadow p-6 flex-1 flex flex-col justify-between" role="region" aria-labelledby="progress-title" aria-describedby="tts-progress-text">
                            <div class="mb-4">
                                <div id="progress-title" class="font-bold text-xl text-gray-800">Ringkasan Perkembangan Terbaru</div>
                            </div>
                            <div class="flex flex-col md:flex-row gap-4 mb-4">
                                <!-- Kognitif -->
                                <div class="flex-1 bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-semibold text-gray-800">Kognitif</span>
                                        <span class="font-bold text-teal-700">{{ $hasProgress ? ($kognitif . '%') : '—' }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-200 rounded mb-2">
                                        <div class="h-2 rounded bg-teal-500" style="width:{{ $hasProgress ? $kognitif : 0 }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-700">Kemampuan belajar dan memahami konsep</div>
                                </div>
                                <!-- Motorik -->
                                <div class="flex-1 bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-semibold text-gray-800">Motorik</span>
                                        <span class="font-bold text-teal-700">{{ $hasProgress ? ($motorik . '%') : '—' }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-200 rounded mb-2">
                                        <div class="h-2 rounded bg-teal-500" style="width:{{ $hasProgress ? $motorik : 0 }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-700">Kemampuan gerakan dan koordinasi</div>
                                </div>
                                <!-- Sosial -->
                                <div class="flex-1 bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-semibold text-gray-800">Sosial</span>
                                        <span class="font-bold text-teal-700">{{ $hasProgress ? ($sosial . '%') : '—' }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-200 rounded mb-2">
                                        <div class="h-2 rounded bg-teal-500" style="width:{{ $hasProgress ? $sosial : 0 }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-700">Kemampuan berinteraksi dengan orang lain</div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-700">
                                {{ $summary ? $summary : 'Belum ada ringkasan perkembangan. Silakan tambahkan penilaian.' }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Grafik Perkembangan -->
                <section class="mb-8" role="region" aria-labelledby="grafik-title">
                    <div class="mb-4">
                        <h2 id="grafik-title" class="font-bold text-2xl text-gray-800">Grafik Perkembangan</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-2xl shadow p-6 min-h-[260px] flex flex-col">
                            <span class="font-bold text-lg text-gray-800 mb-2">Perkembangan Bulanan</span>
                            <div class="flex-1">
                                @if(isset($semesterAverages) && $semesterAverages->count())
                                    <ul class="space-y-3">
                                        @foreach($semesterAverages as $s)
                                            <li class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                                <div class="font-semibold text-gray-700">{{ $s->label ?? 'Tahun' }}</div>
                                                <div class="text-sm text-gray-600">Nilai Rapor Kumulatif: <span class="font-bold text-teal-700">{{ $s->ipk }}</span></div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-sm text-gray-500">Belum ada data Nilai Rapor Kumulatif.</div>
                                @endif
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl shadow p-6 min-h-[260px] flex flex-col" role="region" aria-labelledby="area-title">
                            <span class="font-bold text-lg text-gray-800 mb-2">Perbandingan Area Kemampuan</span>
                            <div class="flex-1">
                                <canvas id="areaRadar" height="220" role="img" aria-label="Radar perbandingan area kemampuan: Motorik, Kognitif, Sosial"></canvas>
                            </div>
                        </div>
                    </div>
                </section>

        <!-- Latest Reports -->
        <section class="mb-6" role="region" aria-labelledby="latest-reports-title">
            <div class="flex items-center justify-between mb-2">
            <div id="latest-reports-title" class="font-semibold text-gray-800">Laporan Terbaru</div>
            <a href="{{ route('siswa.laporan.index') }}"
                class="text-sm text-teal-700 hover:underline focus:outline-none focus:ring-2 focus:ring-teal-500" aria-label="Lihat semua laporan">Lihat
                Semua &rarr;</a>
            </div>
                    <div class="space-y-4">

                        @php
                            // Prefer the $student passed to the view; fall back to authenticated user's student_id
                            $ownerId = isset($student) && $student ? ($student->id ?? null) : (auth()->check() ? (auth()->user()->student_id ?? null) : null);
                            // Ensure we only show reports for the intended student and limit to 3 latest
                            $latestReports = collect($recentReports ?? collect());
                            if ($ownerId) {
                                $latestReports = $latestReports->where('student_id', $ownerId);
                            }
                            $latestReports = $latestReports->sortByDesc('created_at')->values()->take(3);
                        @endphp

                        @if($latestReports && $latestReports->count())
                            @foreach($latestReports as $rep)
                                <div class="bg-white rounded-xl shadow p-4 flex flex-col md:flex-row md:items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-gray-700">{{ $rep->month_label ?? ($rep->created_at ? \Carbon\Carbon::parse($rep->created_at)->format('F Y') : 'Laporan') }}</div>
                                        <div class="text-xs text-gray-500 mb-1">{{ Str::limit($rep->notes ?? 'Ringkasan penilaian', 80) }}</div>
                                        <div class="text-xs text-gray-600">Kognitif: {{ $rep->kognitif ?? '-' }} • Motorik: {{ $rep->motorik ?? '-' }} • Sosial: {{ $rep->sosial ?? '-' }}</div>
                                    </div>
                                    <div class="flex flex-col items-end mt-2 md:mt-0">
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-semibold mb-1">{{ $rep->status ?? 'Selesai' }}</span>
                                        <a href="{{ route('student.grades', ['open' => $rep->id]) }}" class="text-xs text-teal-700 hover:underline focus:outline-none focus:ring-2 focus:ring-teal-500" aria-label="Lihat detail laporan {{ $rep->month_label ?? '' }}">Lihat Detail</a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-sm text-gray-500">Belum ada laporan penilaian.</div>
                        @endif
                    </div>
                </section>

                <!-- Upcoming Activities & Communication -->
                <section class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <div class="font-semibold text-gray-800 mb-2">Kegiatan Mendatang</div>
                        <div class="bg-white rounded-xl shadow p-4 space-y-3">
                            @foreach ($activities as $item)
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-block w-8 h-8 rounded bg-teal-100 text-teal-700 text-center font-bold">
                                        {{ \Carbon\Carbon::parse($item->activity_date)->format('d') }}
                                    </span>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-700">
                                            {{ $item->title }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $item->description }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if ($activities->isEmpty())
                                <div class="text-sm text-gray-500">Tidak ada kegiatan mendatang.</div>
                            @endif

                            <a href="#"
                                class="text-xs text-teal-700 hover:underline focus:outline-none focus:ring-2 focus:ring-teal-500">
                                Lihat Semua Kegiatan
                            </a>
                        </div>

                    </div>
                    {{-- <div>
                        <div class="font-semibold text-gray-800 mb-2">Komunikasi dengan Guru</div>
                        <div class="bg-white rounded-xl shadow p-4 space-y-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-700">Pesan Terakhir</span>
                                <button
                                    class="px-3 py-1 rounded bg-teal-600 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500">+
                                    Kirim Pesan</button>
                            </div>
                            <div class="border-b pb-2 mb-2">
                                <div class="text-xs text-gray-600">Bu Elly: Silakan cek laporan terbaru di dashboard.
                                    Jika ada pertanyaan, jangan ragu untuk menghubungi saya.</div>
                                <div class="text-xs text-gray-400 mt-1">2 hari lalu</div>
                            </div>
                            <div class="border-b pb-2 mb-2">
                                <div class="text-xs text-gray-600">Anda: Terima kasih Bu, kami akan cek dan diskusikan
                                    di rumah.</div>
                                <div class="text-xs text-gray-400 mt-1">1 hari lalu</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600">Bu Elly: Jangan lupa untuk mengisi jurnal harian
                                    perkembangan anak.</div>
                                <div class="text-xs text-gray-400 mt-1">1 jam lalu</div>
                            </div>
                            <a href="#"
                                class="text-xs text-teal-700 hover:underline focus:outline-none focus:ring-2 focus:ring-teal-500">Lihat
                                Semua Pesan</a>
                        </div>
                    </div> --}}
                </section>

            </div>
        </div>
    </main>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        const data = @json($areaHistory ?? []);
        if (!data || !data.length) return;

        // labels are the months
        const labels = data.map(d => d.label);

        // For radar we want three axes: Motorik, Kognitif, Sosial.
        // Each dataset is one month. Chart.js radar expects labels for axes and datasets for each month.
        const axes = ['Motorik', 'Kognitif', 'Sosial'];

        const datasets = data.map((d, idx) => ({
            label: d.label,
            data: [d.motorik ?? 0, d.kognitif ?? 0, d.sosial ?? 0],
            fill: true,
            backgroundColor: `hsla(${(idx * 60) % 360}, 70%, 50%, 0.15)`,
            borderColor: `hsl(${(idx * 60) % 360}, 70%, 45%)`,
            pointBackgroundColor: `hsl(${(idx * 60) % 360}, 70%, 45%)`,
            pointRadius: 3,
        }));

        const ctx = document.getElementById('areaRadar').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: axes,
                datasets: datasets
            },
            options: {
                elements: { line: { tension: 0.2 } },
                scale: {
                    ticks: { beginAtZero: true, max: 100 }
                },
                plugins: {
                    legend: { position: 'bottom' }
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    })();
</script>
