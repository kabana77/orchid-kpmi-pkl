<?php

namespace App\Http\Requests\Referral;

use App\Models\Referral;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeReferralStatusRequest extends FormRequest
{
    /** Otorisasi di layar: Gate::authorize('changeStatus', $referral). */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // Hanya memastikan nilainya status yang dikenal.
            // Boleh atau tidaknya berpindah ditentukan ReferralService (tabel TRANSITIONS).
            'status' => ['required', Rule::in(array_keys(Referral::LABELS))],
        ];
    }

    public function attributes(): array
    {
        return ['status' => 'status'];
    }
}
