<?php

namespace App\Services;

use App\Models\Opportunity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OpportunityBoardService
{
    private const PEMASANG_COLUMNS = 'pemasang:id,nama_lengkap,kota,foto,status_aktif';

    public function paginate(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return Opportunity::query()
            ->terbit()
            ->dariAnggotaAktif()
            ->with(self::PEMASANG_COLUMNS)
            ->filterPublik($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Data halaman detail. Draft dan peluang milik anggota nonaktif/terhapus menghasilkan 404.
     * Peluang Closed atau yang sudah lewat batas waktu tetap bisa dibuka (tanpa kontak).
     *
     * @return array{opportunity: Opportunity, member: \App\Models\Member, related: Collection}
     */
    public function detail(Opportunity $opportunity): array
    {
        abort_unless(in_array(strtolower((string) $opportunity->status), ['published', 'closed'], true), 404);

        $opportunity->load('pemasang.professionalRoles:id,nama');
        $member = $opportunity->pemasang;
        abort_unless($member?->status_aktif, 404);

        $tipe = strtolower((string) $opportunity->tipe);

        return [
            'opportunity' => $opportunity,
            'member' => $member,
            'related' => Opportunity::query()
                ->terbit()
                ->dariAnggotaAktif()
                ->with(self::PEMASANG_COLUMNS)
                ->whereKeyNot($opportunity->getKey())
                ->orderByRaw('(LOWER(tipe) = ?) desc', [$tipe])
                ->latest()
                ->limit(2)
                ->get(),
        ];
    }
}
