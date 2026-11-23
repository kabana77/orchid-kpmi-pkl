<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class MemberDirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // halaman publik, tanpa login
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:100'],
            'skill' => ['nullable', 'integer', 'exists:skills,id'],
            'peran' => ['nullable', 'array'],
            'peran.*' => ['integer', 'exists:professional_roles,id'],
        ];
    }
}
