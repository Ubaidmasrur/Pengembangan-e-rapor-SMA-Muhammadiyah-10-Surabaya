<x-app-layout>
    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#academic-year-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to
                form</a>

            <h1 class="text-2xl font-bold mb-4" id="form-title">Ubah Data Tahun Ajaran</h1>

            <section aria-labelledby="form-title">
                <form id="academic-year-form" method="POST"
                    action="{{ route('admin.academic_years.update', $academicYear) }}" class="space-y-4"
                    onsubmit="return combineYearsAndMonths()">
                    @csrf
                    @method('PUT')

                    {{-- Hidden Final Value --}}
                    <input type="hidden" name="year" id="year" value="{{ $academicYear->year }}">
                    <input type="hidden" name="start_month" id="start_month"
                        value="{{ old('start_month', $academicYear->start_month) }}">
                    <input type="hidden" name="end_month" id="end_month"
                        value="{{ old('end_month', $academicYear->end_month) }}">

                    {{-- Semester --}}
                    <x-form-select name="semester" label="Semester" :options="['Ganjil' => 'Ganjil', 'Genap' => 'Genap']" :value="old('semester', $academicYear->semester)" />

                    {{-- Bulan Mulai (Dropdown) --}}
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
                        ]" :value="old(
                            'start_month_select',
                            \Carbon\Carbon::parse($academicYear->start_month)->format('m'),
                        )" />

                    <x-form-select name="start_year_select" id="start_year_select" label="Tahun Bulan Mulai"
                        :options="collect(range(now()->year - 5, now()->year + 5))
                            ->mapWithKeys(fn($y) => [$y => $y])
                            ->toArray()" :value="old(
                            'start_year_select',
                            \Carbon\Carbon::parse($academicYear->start_month)->format('Y'),
                        )" />

                    {{-- Bulan Akhir (Dropdown) --}}
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
                        :value="old(
                            'end_month_select',
                            \Carbon\Carbon::parse($academicYear->end_month)->format('m'),
                        )" />

                    <x-form-select name="end_year_select" id="end_year_select" label="Tahun Bulan Akhir"
                        :options="collect(range(now()->year - 5, now()->year + 5))
                            ->mapWithKeys(fn($y) => [$y => $y])
                            ->toArray()" :value="old('end_year_select', \Carbon\Carbon::parse($academicYear->end_month)->format('Y'))" />

                    {{-- Status Aktif --}}
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            class="rounded border-gray-300 shadow-sm focus:ring focus:ring-blue-200"
                            {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}
                            aria-checked="{{ old('is_active', $academicYear->is_active) ? 'true' : 'false' }}">
                        <label for="is_active" class="ml-2 text-base text-gray-700">
                            Tandai sebagai tahun ajaran aktif
                        </label>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-2 mt-4">
                        <x-buttons.back :href="route('admin.academic_years.index')" />
                        <x-buttons.save label="Update" />
                    </div>
                </form>
            </section>
        </div>
    </main>

    <footer class="sr-only" aria-hidden="true"></footer>

    {{-- Script --}}
    <script>
        function combineYearsAndMonths() {
            const form = document.getElementById('academic-year-form');

            const start = form.querySelector('[name=start_year_ui]').value;
            const end = form.querySelector('[name=end_year_ui]').value;
            if ((start && end) && (parseInt(start) < parseInt(end))) {
                form.querySelector('[name=year]').value = `${start}/${end}`;
            }

            const sm = form.querySelector('#start_month_select')?.value;
            const sy = form.querySelector('#start_year_select')?.value;
            const em = form.querySelector('#end_month_select')?.value;
            const ey = form.querySelector('#end_year_select')?.value;

            form.querySelector('[name=start_month]').value = `${sy}-${sm}`;
            form.querySelector('[name=end_month]').value = `${ey}-${em}`;

            return true;
        }
    </script>
</x-app-layout>
