<x-app-layout>
    <main>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="#activity-form" class="sr-only focus:not-sr-only focus:ring focus:ring-blue-400">Skip to form</a>
            <h1 class="text-2xl font-bold mb-4" id="form-title">Edit Kegiatan</h1>

            <section aria-labelledby="form-title">
                <form id="activity-form" method="POST" action="{{ route('admin.activities.update', $activity->id) }}"
                    enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Tanggal --}}
                    <x-form-input name="activity_date" label="Tanggal" type="date" :value="old('activity_date', optional($activity->activity_date)->format('Y-m-d'))"
                        aria-label="Tanggal Kegiatan" />

                    {{-- Judul --}}
                    <x-form-input name="title" label="Judul" type="text" :value="old('title', $activity->title)"
                        aria-label="Judul Kegiatan" />

                    {{-- Deskripsi --}}
                    <x-form-textarea name="description" label="Deskripsi" rows="4" :value="old('description', $activity->description)"
                        aria-label="Deskripsi Kegiatan" />

                    {{-- Thumbnail (dengan pratinjau saat ini) --}}
                    <x-image-preview name="thumbnail" label="Thumbnail Kegiatan" :current="$activity->thumbnail" />

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
