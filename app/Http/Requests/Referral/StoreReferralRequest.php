<?php

namespace App\Http\Requests\Referral;

use App\Models\Referral;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Referral::class) ?? false;
    }

    public function rules(): array
    {
        $anggotaAda = Rule::exists('members', 'id')->whereNull('deleted_at');

        $rules = [
            'referral.penerima_referral_id' => ['required', 'integer', $anggotaAda],
            'referral.client_name'          => ['required', 'string', 'max:255'],
            'referral.project_name'         => ['required', 'string', 'max:255'],
            'referral.nilai_estimasi'       => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'referral.catatan'              => ['nullable', 'string', 'max:2000'],
        ];

        // Hanya admin yang boleh memilih pemberi. Untuk anggota biasa, field ini
        // sengaja tidak divalidasi sehingga tidak ikut di validated(); pemberi
        // otomatis diambil dari akun yang login (lihat ReferralService).
        if ($this->user()?->hasAccess('platform.referrals')) {
            $rules['referral.pemberi_referral_id'] = ['nullable', 'integer', $anggotaAda];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'referral.penerima_referral_id' => 'penerima referral',
            'referral.pemberi_referral_id'  => 'pemberi referral',
            'referral.client_name'          => 'nama klien',
            'referral.project_name'         => 'nama proyek',
            'referral.nilai_estimasi'       => 'nilai estimasi',
            'referral.catatan'              => 'catatan',
        ];
    }

    /** Data bersih untuk dikirim ke ReferralService. */
    public function referralData(): array
    {
        return $this->validated()['referral'];
    }
}
