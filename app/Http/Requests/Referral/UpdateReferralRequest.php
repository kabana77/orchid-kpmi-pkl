<?php

namespace App\Http\Requests\Referral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReferralRequest extends FormRequest
{
    /**
     * Otorisasi dilakukan di layar lewat Gate::authorize('update', $referral),
     * karena butuh model yang sedang diedit. Pemberi referral tidak bisa diganti.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'referral.penerima_referral_id' => [
                'required', 'integer',
                Rule::exists('members', 'id')->whereNull('deleted_at'),
            ],
            'referral.client_name'    => ['required', 'string', 'max:255'],
            'referral.project_name'   => ['required', 'string', 'max:255'],
            'referral.nilai_estimasi' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'referral.catatan'        => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'referral.penerima_referral_id' => 'penerima referral',
            'referral.client_name'          => 'nama klien',
            'referral.project_name'         => 'nama proyek',
            'referral.nilai_estimasi'       => 'nilai estimasi',
            'referral.catatan'              => 'catatan',
        ];
    }

    public function referralData(): array
    {
        return $this->validated()['referral'];
    }
}
