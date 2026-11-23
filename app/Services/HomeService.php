<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Member;
use App\Models\Opportunity;
use Illuminate\Support\Facades\Cache;

class HomeService
{
    /**
     * @return array{stats: array<string, int>, recentOpportunities: \Illuminate\Support\Collection, latestMembers: \Illuminate\Support\Collection}
     */
    public function data(): array
    {
        return [
            // Counter disimpan 5 menit supaya beranda tidak menghitung ulang di setiap kunjungan.
            'stats' => Cache::remember('public.home.stats', now()->addMinutes(5), fn () => [
                'anggota' => Member::query()->aktif()->count(),
                'usaha' => Business::query()->dariAnggotaAktif()->aktif()->count(),
                'peluang' => Opportunity::query()->terbit()->dariAnggotaAktif()->count(),
            ]),
            'recentOpportunities' => Opportunity::query()
                ->terbit()
                ->dariAnggotaAktif()
                ->latest()
                ->limit(3)
                ->get(),
            'latestMembers' => Member::query()
                ->aktif()
                ->with('professionalRoles:id,nama')
                ->latest()
                ->limit(4)
                ->get(),
        ];
    }
}
