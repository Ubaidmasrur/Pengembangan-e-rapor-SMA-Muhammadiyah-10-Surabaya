@props([
    'name' => 'thumbnail',
    'label' => 'Thumbnail',
    'previewId' => 'image-preview',
    'wrapperId' => 'image-preview-wrapper',
    'current' => null,
])

<div class="space-y-2">
    <label for="{{ $name }}" class="block font-medium text-base text-gray-700">{{ $label }}</label>

    <input type="file" name="{{ $name }}" id="{{ $name }}" accept="image/*"
        onchange="previewImage(event, '{{ $previewId }}', '{{ $wrapperId }}')"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 focus:outline-none @error($name) border-red-500 ring-2 ring-red-400 @enderror" />

    @error($name)
        <span class="flex items-center text-red-600 text-sm" role="alert">{{ $message }}</span>
    @enderror

    {{-- Preview Container --}}
    <div id="{{ $wrapperId }}" class="mt-2 {{ $current ? '' : 'hidden' }}">
        <img id="{{ $previewId }}" src="{{ $current ? asset('storage/' . $current) : '' }}" alt="Preview"
            class="w-40 h-auto rounded border border-gray-300">
    </div>

    {{-- Hapus Checkbox jika gambar ada --}}
    @if ($current)
        <div class="mt-2">
            <input type="checkbox" name="delete_{{ $name }}" id="delete_{{ $name }}" value="1"
                class="mr-2 text-red-600 border-gray-300 focus:ring-red-500">
            <label for="delete_{{ $name }}" class="text-sm text-red-700">Hapus gambar saat ini</label>
        </div>
    @endif

    {{-- Script Preview --}}
    <script>
        function previewImage(event, previewId, wrapperId) {
            const input = event.target;
            const preview = document.getElementById(previewId);
            const wrapper = document.getElementById(wrapperId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    wrapper.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                wrapper.classList.add('hidden');
            }
        }
    </script>
</div>
