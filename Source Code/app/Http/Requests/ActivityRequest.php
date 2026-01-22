<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string'],
            'activity_date'  => ['nullable', 'date'],
            'thumbnail'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'description.required' => 'Deskripsi kegiatan wajib diisi.',
            'thumbnail.image' => 'File harus berupa gambar.',
            'thumbnail.mimes' => 'Gambar harus berformat jpg, jpeg, png, atau webp.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
