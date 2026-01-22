<x-app-layout>
    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#activity-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to form</a>
            <h1 class="text-2xl font-bold mb-4" id="form-title">Tambah Kegiatan</h1>

            {{-- Alert sukses standar --}}
            <x-alert.success />

            <section aria-labelledby="form-title">
                <form id="activity-form" method="POST" action="{{ route('admin.activities.store') }}"
                    enctype="multipart/form-data" class="space-y-4" role="form" aria-label="Form Tambah Kegiatan">
                    @csrf

                    {{-- Tanggal --}}
                    <x-form-input name="activity_date" label="Tanggal" type="date" :value="old('activity_date')"
                        aria-label="Tanggal Kegiatan" />

                    {{-- Judul --}}
                    <x-form-input name="title" label="Judul" type="text" :value="old('title')"
                        aria-label="Judul Kegiatan" />

                    {{-- Deskripsi --}}
                    <x-form-textarea name="description" label="Deskripsi" rows="4" :value="old('description')"
                        aria-label="Deskripsi Kegiatan" />

                    {{-- Thumbnail --}}
                    <x-image-preview name="thumbnail" label="Thumbnail Kegiatan" />

                    {{-- Aksi --}}
                    <div class="flex justify-end gap-2">
                        <x-buttons.back :href="route('admin.activities.index')" label="Batal" />
                        <x-buttons.save label="Simpan" />
                    </div>
                </form>
            </section>
        </div>
    </main>
</x-app-layout>
