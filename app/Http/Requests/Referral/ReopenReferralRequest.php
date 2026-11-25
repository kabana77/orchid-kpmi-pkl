<?php

namespace App\Http\Requests\Referral;

use App\Models\Referral;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReopenReferralRequest extends FormRequest
{
    /** Otorisasi di layar: Gate::authorize('reopen', $referral). Hanya admin. */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                Referral::INTRODUCED,
                Referral::FOLLOW_UP,
                Referral::NEGOTIATION,
            ])],
        ];
    }

    public function attributes(): array
    {
        return ['status' => 'status tujuan'];
    }
}
