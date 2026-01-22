<x-app-layout>
    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#academic-year-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">
                Skip to form
            </a>

            <h1 class="text-2xl font-bold mb-4" id="form-title">Tambah Tahun Pelajaran</h1>

            <section aria-labelledby="form-title">
                <form id="academic-year-form" method="POST" action="{{ route('admin.academic_years.store') }}"
                    class="space-y-4" onsubmit="return combineYearsAndMonths()">
                    @csrf

                    {{-- Field tersembunyi untuk menyimpan format tahun dan bulan --}}
                    <input type="hidden" name="year" id="year">

                    {{-- Select Semester --}}
                    <x-form-select name="semester" label="Semester" :options="['Ganjil' => 'Ganjil', 'Genap' => 'Genap']" :value="old('semester')" />

                    {{-- Hidden fields --}}
                    <input type="hidden" name="start_month" id="start_month">
                    <input type="hidden" name="end_month" id="end_month">

                    {{-- Bulan & Tahun Mulai --}}
                    <x-form-select name="start_month_select" id="start_month_select" label="Bulan Mulai"
                        :options="[
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember',
                        ]" :value="old('start_month_select')" />

                    <x-form-select name="start_year_select" id="start_year_select" label="Tahun Bulan Mulai"
                        :options="collect(range(now()->year - 5, now()->year + 5))
                            ->mapWithKeys(fn($y) => [$y => $y])
                            ->toArray()" :value="old('start_year_select')" />

                    {{-- Bulan & Tahun Akhir --}}
                    <x-form-select name="end_month_select" id="end_month_select" label="Bulan Akhir" :options="[
                        '01' => 'Januari',
                        '02' => 'Februari',
                        '03' => 'Maret',
                        '04' => 'April',
                        '05' => 'Mei',
                        '06' => 'Juni',
                        '07' => 'Juli',
                        '08' => 'Agustus',
                        '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ]"
                        :value="old('end_month_select')" />

                    <x-form-select name="end_year_select" id="end_year_select" label="Tahun Bulan Akhir"
                        :options="collect(range(now()->year - 5, now()->year + 5))
                            ->mapWithKeys(fn($y) => [$y => $y])
                            ->toArray()" :value="old('end_year_select')" />

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-2 mt-4">
                        <x-buttons.back :href="route('admin.academic_years.index')" />
                        <x-buttons.save label="Tambah" />
                    </div>
                </form>

                @if (session('success'))
                    <div class="mt-4 p-3 rounded bg-green-100 text-green-800 border border-green-300">
                        {{ session('success') }}
                    </div>
                @endif
            </section>
        </div>
    </main>

    {{-- Script JS --}}
    <script>
        function combineYearsAndMonths() {
            const form = document.getElementById('academic-year-form');

            const sm = form.querySelector('#start_month_select')?.value;
            const sy = form.querySelector('#start_year_select')?.value;
            const em = form.querySelector('#end_month_select')?.value;
            const ey = form.querySelector('#end_year_select')?.value;

            const startMonthField = form.querySelector('[name="start_month"]');
            const endMonthField = form.querySelector('[name="end_month"]');

            const smFormatted = sm?.toString().padStart(2, '0');
            const emFormatted = em?.toString().padStart(2, '0');

            if (sy && ey) {
                form.querySelector('[name="year"]').value = sy + '/' + ey;
            }

            if (sy && sm) {
                form.querySelector('[name="start_month"]').value = sy + '-' + smFormatted;
            }

            if (ey && em) {
                form.querySelector('[name="end_month"]').value = ey + '-' + emFormatted;
            }
            console.log("tahun " + sy + '/' + ey);
            return true;
        }
    </script>


</x-app-layout>
