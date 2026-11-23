<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Member;
use App\Models\ProfessionalRole;
use App\Models\Skill;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MemberDirectoryService
{
    public function paginate(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return Member::query()
            ->aktif()
            ->with(['professionalRoles:id,nama', 'skills:id,nama'])
            ->filter($filters)
            ->orderBy('nama_lengkap')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return array{kota: Collection, skills: Collection, roles: Collection} */
    public function filterOptions(): array
    {
        return [
            'kota' => Member::query()
                ->aktif()
                ->whereNotNull('kota')
                ->where('kota', '!=', '')
                ->distinct()
                ->orderBy('kota')
                ->pluck('kota'),
            'skills' => Skill::query()->orderBy('nama')->get(['id', 'nama']),
            'roles' => ProfessionalRole::query()->orderBy('nama')->get(['id', 'nama']),
        ];
    }

    /**
     * Data halaman detail anggota.
     * Usaha Aktif tampil lebih dulu; usaha Non Aktif tetap tampil sebagai riwayat.
     *
     * @return array{member: Member, businesses: Collection, opportunities: Collection}
     */
    public function profile(Member $member): array
    {
        $member->load(['professionalRoles:id,nama', 'skills:id,nama']);

        return [
            'member' => $member,
            'businesses' => $member->businesses()
                ->orderBy('nama_usaha')
                ->get()
                ->sortBy(fn (Business $business) => strtolower((string) $business->status) === 'aktif' ? 0 : 1)
                ->values(),
            'opportunities' => $member->opportunities()->aktif()->latest()->get(),
        ];
    }
}
