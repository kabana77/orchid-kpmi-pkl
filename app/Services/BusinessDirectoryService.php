<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BusinessDirectoryService
{
    public function paginate(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return Business::query()
            ->dariAnggotaAktif()
            ->with('member:id,nama_lengkap,kota')
            ->filter($filters)
            ->orderBy('nama_usaha')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return array{kategori: Collection} */
    public function filterOptions(): array
    {
        return [
            'kategori' => Business::query()
                ->dariAnggotaAktif()
                ->whereNotNull('kategori')
                ->where('kategori', '!=', '')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori'),
        ];
    }

    /**
     * Data halaman detail usaha. Pemilik harus sudah dipastikan aktif oleh controller.
     *
     * @return array{business: Business, member: \App\Models\Member, otherBusinesses: Collection, opportunities: Collection}
     */
    public function profile(Business $business): array
    {
        $business->load('member.professionalRoles:id,nama');
        $member = $business->member;

        return [
            'business' => $business,
            'member' => $member,
            'otherBusinesses' => $member->businesses()
                ->whereKeyNot($business->getKey())
                ->orderBy('nama_usaha')
                ->limit(5)
                ->get(),
            'opportunities' => $member->opportunities()->terbit()->latest()->get(),
        ];
    }
}
