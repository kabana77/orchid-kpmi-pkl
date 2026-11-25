<?php

namespace App\Services\Referral;

use App\Models\Member;
use App\Models\Referral;
use App\Models\User;
use DomainException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Aturan bisnis Referral. Konvensi pemakaian (di layar Orchid maupun tempat lain):
 *   1. Gate::authorize('ability', $referral)   -> "boleh atau tidak" (Policy)
 *   2. $service->metode(...)                   -> menjalankan + memeriksa ulang di bawah kunci
 *
 * Catatan: metode yang mengubah data mengembalikan model SEGAR (hasil kunci).
 * Model lama yang dipegang pemanggil menjadi basi, jadi pakai nilai kembaliannya.
 */
class ReferralService
{
    private const ADMIN = 'platform.referrals';

    public function create(User $actor, array $data): Referral
    {
        $pemberiId  = $this->resolvePemberiId($actor, $data);
        $penerimaId = (int) ($data['penerima_referral_id'] ?? 0);

        $this->assertDifferent($pemberiId, $penerimaId);
        $this->assertActiveMember($pemberiId, 'Pemberi referral');
        $this->assertActiveMember($penerimaId, 'Penerima referral');

        return DB::transaction(function () use ($data, $pemberiId) {
            $referral = new Referral(Arr::only($data, (new Referral)->getFillable()));

            // Kolom yang tidak mass-assignable diisi eksplisit di sini, hanya oleh service.
            $referral->pemberi_referral_id = $pemberiId;
            $referral->status              = Referral::INTRODUCED;
            $referral->status_diubah_at    = now();
            $referral->save();

            return $referral;
        });
    }

    public function update(User $actor, Referral $referral, array $data): Referral
    {
        $data = Arr::only($data, (new Referral)->getFillable());

        return DB::transaction(function () use ($actor, $referral, $data) {
            $locked = $this->lock($referral);

            // Periksa ulang di bawah kunci: data di layar bisa sudah basi.
            if (! $this->isAdmin($actor) && $locked->status !== Referral::INTRODUCED) {
                throw new DomainException('Referral sudah diproses, datanya terkunci. Hubungi admin untuk perubahan.');
            }

            if (isset($data['penerima_referral_id'])
                && (int) $data['penerima_referral_id'] !== (int) $locked->penerima_referral_id) {
                $this->assertDifferent((int) $locked->pemberi_referral_id, (int) $data['penerima_referral_id']);
                $this->assertActiveMember((int) $data['penerima_referral_id'], 'Penerima referral');
            }

            $locked->update($data);

            return $locked;
        });
    }

    public function changeStatus(Referral $referral, string $to): Referral
    {
        return DB::transaction(function () use ($referral, $to) {
            $locked = $this->lock($referral);

            if (! $locked->canMoveTo($to)) {
                throw new DomainException(sprintf(
                    'Status tidak boleh berpindah dari %s ke %s.',
                    $locked->statusLabel(),
                    Referral::LABELS[$to] ?? $to
                ));
            }

            return $this->setStatus($locked, $to);
        });
    }

    /** Membuka kembali Won/Lost. Izin (hanya admin) diperiksa Policy sebelum memanggil ini. */
    public function reopen(Referral $referral, string $to): Referral
    {
        $allowed = [Referral::INTRODUCED, Referral::FOLLOW_UP, Referral::NEGOTIATION];

        if (! in_array($to, $allowed, true)) {
            throw new DomainException('Referral hanya bisa dibuka kembali ke Introduced, Follow Up, atau Negotiation.');
        }

        return DB::transaction(function () use ($referral, $to) {
            $locked = $this->lock($referral);

            if (! $locked->isFinal()) {
                throw new DomainException('Referral ini belum Won atau Lost, tidak perlu dibuka kembali.');
            }

            return $this->setStatus($locked, $to);
        });
    }

    public function delete(Referral $referral): bool
    {
        return (bool) DB::transaction(fn () => $referral->delete());
    }

    public function restore(int $id): Referral
    {
        $referral = Referral::withTrashed()->findOrFail($id);
        $referral->restore();

        return $referral;
    }

    // ---------------------------------------------------------------- helper

    private function lock(Referral $referral): Referral
    {
        return Referral::query()->lockForUpdate()->findOrFail($referral->id);
    }

    private function setStatus(Referral $referral, string $to): Referral
    {
        $referral->status           = $to;
        $referral->status_diubah_at = now();
        $referral->save();

        return $referral;
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasAccess(self::ADMIN);
    }

    /** Anggota biasa: selalu dirinya sendiri. Admin: boleh memilih pemberi. */
    private function resolvePemberiId(User $actor, array $data): int
    {
        $id = $this->isAdmin($actor)
            ? ($data['pemberi_referral_id'] ?? $actor->member?->id)
            : $actor->member?->id;

        if (! $id) {
            throw new DomainException('Pemberi referral belum ditentukan.');
        }

        return (int) $id;
    }

    private function assertDifferent(int $pemberiId, int $penerimaId): void
    {
        if ($pemberiId === $penerimaId) {
            throw new DomainException('Pemberi dan penerima referral tidak boleh orang yang sama.');
        }
    }

    // Member::query() sudah mengecualikan yang di-soft-delete.
    private function assertActiveMember(int $memberId, string $label): void
    {
        $ok = Member::query()->whereKey($memberId)->where('status_aktif', true)->exists();

        if (! $ok) {
            throw new DomainException("$label harus anggota yang aktif.");
        }
    }
}
