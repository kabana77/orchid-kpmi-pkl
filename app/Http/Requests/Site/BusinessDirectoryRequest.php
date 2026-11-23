<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class BusinessDirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // halaman publik, tanpa login
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:aktif,nonaktif,semua'],
        ];
    }
}
