<x-app-layout>
    {{-- <header>
        <nav aria-label="Breadcrumb">
            <ol class="flex text-sm mb-4" role="list">
                <li><a href="{{ route('admin.dashboard') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Dashboard</a></li>
                <li class="mx-2">/</li>
                <li><a href="{{ route('admin.grade_parameters.index') }}"
                        class="text-blue-700 underline focus:ring focus:ring-blue-400">Parameter Nilai</a></li>
                <li class="mx-2">/</li>
                <li aria-current="page" class="text-gray-700">Tambah</li>
            </ol>
        </nav>
    </header> --}}

    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#grade-parameter-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to
                form</a>
            <h1 class="text-2xl font-bold mb-4" id="form-title">Tambah Parameter Nilai</h1>

            <section aria-labelledby="form-title">
                <form id="grade-parameter-form" role="form" aria-labelledby="form-title" method="POST"
                    action="{{ route('admin.grade_parameters.store') }}" class="space-y-4">
                    @csrf

                    {{-- Huruf Nilai --}}
                    <x-form-select name="grade_letter" label="Huruf Nilai" :options="['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E']" :value="old('grade_letter')" />

                    {{-- Skor Minimum --}}
                    <x-form-input name="min_score" label="Skor Minimum" type="number" :value="old('min_score')" />

                    {{-- Skor Maksimum --}}
                    <x-form-input name="max_score" label="Skor Maksimum" type="number" :value="old('max_score')" />

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-2 mt-4">
                        <x-buttons.back :href="route('admin.grade_parameters.index')" />
                        <x-buttons.save />
                    </div>
                </form>
            </section>
        </div>
    </main>

    <footer class="sr-only"></footer>
</x-app-layout>
