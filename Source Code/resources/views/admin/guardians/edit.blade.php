<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold mb-4">Edit Wali Murid</h1>

        <form method="POST" action="{{ route('admin.guardians.update', $guardian) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="student_id" class="block font-medium text-sm text-gray-700">Pilih Murid</label>
                <select name="student_id" id="student_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('student_id') border-red-500 ring-red-500 @enderror">
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}"
                            {{ old('student_id', $guardian->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <x-input-error :message="$message" />
                @enderror
            </div>

            <div>
                <label for="name" class="block font-medium text-sm text-gray-700">Nama Wali</label>
                <input type="text" name="name" id="name" value="{{ old('name', $guardian->name) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 ring-red-500 @enderror">
                @error('name')
                    <x-input-error :message="$message" />
                @enderror
            </div>

            <div>
                <label for="relationship" class="block font-medium text-sm text-gray-700">Hubungan</label>
                <select name="relationship" id="relationship"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 focus:outline-none @error('relationship') border-red-500 ring-2 ring-red-400 @enderror"
                    aria-label="Pilih hubungan">
                    @foreach (['Orang Tua', 'Saudara'] as $relationship)
                        <option value="{{ $relationship }}"
                            {{ old('relationship', $guardian->relationship) == $relationship ? 'selected' : '' }}>
                            {{ $relationship }}</option>
                    @endforeach
                </select>
                @error('relationship')
                    <x-input-error :message="$message" />
                @enderror
            </div>

            <div>
                <label for="phone" class="block font-medium text-sm text-gray-700">Nomor Telepon (Opsional)</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $guardian->phone) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 ring-red-500 @enderror">
                @error('phone')
                    <x-input-error :message="$message" />
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.guardians.index') }}"
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</x-app-layout>
