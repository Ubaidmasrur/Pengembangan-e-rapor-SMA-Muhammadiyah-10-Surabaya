<x-app-layout>
    <main aria-labelledby="dashboard-title" role="main" tabindex="-1">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Dashboard Title -->
                <header role="banner" class="mb-6">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight" id="dashboard-title">
                        {{ __('Dashboard') }}
                    </h1>
                </header>

                <section aria-label="Statistik Sekolah" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 justify-center">
                        <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center" tabindex="0"
                            aria-label="Total Siswa">
                            <span class="text-gray-500 text-sm mb-2">Total Siswa</span>
                            <span class="text-2xl font-bold text-gray-800">{{ $totalStudents }}</span>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center" tabindex="0"
                            aria-label="Total Guru">
                            <span class="text-gray-500 text-sm mb-2">Total Guru</span>
                            <span class="text-2xl font-bold text-gray-800">{{ $totalTeachers }}</span>
                        </div>
                        {{-- <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center" tabindex="0"
                            aria-label="Laporan Dibuat">
                            <span class="text-gray-500 text-sm mb-2">Laporan Dibuat</span>
                            <span class="text-2xl font-bold text-gray-800">{{ $reportsCreated }}</span>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center" tabindex="0"
                            aria-label="Menunggu Evaluasi">
                            <span class="text-gray-500 text-sm mb-2">Menunggu Evaluasi</span>
                            <span class="text-2xl font-bold text-gray-800">{{ $reportsPending }}</span>
                        </div> --}}
                    </div>
                </section>

                <!-- Charts & Visualization -->
                <section aria-label="Grafik Perkembangan" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white rounded-lg shadow p-4 flex flex-col" tabindex="0" aria-label="Grafik Perkembangan">
                                <span class="font-semibold text-gray-800 mb-2 block">Grafik Perkembangan</span>
                                <div class="flex-1 w-full bg-gray-50 rounded border" style="min-height:320px;display:flex;align-items:center;justify-content:center;">
                                    <canvas id="chartPerkembangan" style="width:100%!important;max-width:100%;height:320px!important;max-height:320px;" class="block"></canvas>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4 flex flex-col" tabindex="0" aria-label="Grafik Perbandingan">
                                <span class="font-semibold text-gray-800 mb-2 block">Grafik Perbandingan</span>
                                <div class="flex-1 w-full bg-gray-50 rounded border" style="min-height:320px;display:flex;align-items:center;justify-content:center;">
                                    <canvas id="chartPerbandingan" style="width:100%!important;max-width:100%;height:320px!important;max-height:320px;" class="block"></canvas>
                                </div>
                            </div>
                    </div>
                    <script>
                        window.perkembanganLabels = @json($perkembanganLabels ?? []);
                        window.perkembanganDatasets = @json($perkembanganDatasets ?? []);
                        // Radar chart: labels = aspek, datasets = tiap semester
                        window.perbandinganLabels = @json($perbandinganLabels ?? []);
                        window.perbandinganDatasets = @json($perbandinganDatasets ?? []);
                    </script>
                </section>

                <!-- Upcoming Activities -->
                <section class="mb-6">
                    <div class="font-semibold text-gray-800 mb-2">Kegiatan Mendatang</div>
                    <div class="bg-white rounded-xl shadow p-4 space-y-3">
                        @foreach ($activities ?? [] as $item)
                            <div class="flex items-center gap-3">
                                <span class="inline-block w-8 h-8 rounded bg-teal-100 text-teal-700 text-center font-bold">
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

                        @if (empty($activities) || (is_countable($activities) && count($activities) === 0))
                            <div class="text-sm text-gray-500">Tidak ada kegiatan mendatang.</div>
                        @endif

                        <a href="#" class="text-xs text-teal-700 hover:underline focus:outline-none focus:ring-2 focus:ring-teal-500">
                            Lihat Semua Kegiatan
                        </a>
                    </div>
                </section>
                </section>

            </div>
        </div>
    </main>
</x-app-layout>
