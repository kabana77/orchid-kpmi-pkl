<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class OpportunityDirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // halaman publik, tanpa login
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'tipe' => ['nullable', 'in:need,offer,collaborate'],
        ];
    }
}
