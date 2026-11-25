<?php

namespace App\Policies;

use App\Models\Referral;
use App\Models\User;

class ReferralPolicy
{
    private const ADMIN  = 'platform.referrals';
    private const MEMBER = 'platform.my.referrals';

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user)
            || ($user->hasAccess(self::MEMBER) && $this->memberId($user) !== null);
    }

    // Data klien dan nilai proyek sensitif: hanya pemberi, penerima, dan admin.
    public function view(User $user, Referral $r): bool
    {
        return $this->isAdmin($user)
            || $this->isPemberi($user, $r)
            || $this->isPenerima($user, $r);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user)
            || ($user->hasAccess(self::MEMBER) && $this->memberId($user) !== null);
    }

    // Mengubah data (klien, proyek, nilai, catatan): pemberi hanya selama Introduced.
    public function update(User $user, Referral $r): bool
    {
        return $this->isAdmin($user)
            || ($this->isPemberi($user, $r) && $r->status === Referral::INTRODUCED);
    }

    // Mengubah status: penerima (atau admin), dan hanya selama belum Won/Lost.
    public function changeStatus(User $user, Referral $r): bool
    {
        return ! $r->isFinal()
            && ($this->isAdmin($user) || $this->isPenerima($user, $r));
    }

    // Membuka kembali Won/Lost: hanya admin.
    public function reopen(User $user, Referral $r): bool
    {
        return $this->isAdmin($user) && $r->isFinal();
    }

    public function delete(User $user, Referral $r): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Referral $r): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasAccess(self::ADMIN);
    }

    private function isPemberi(User $user, Referral $r): bool
    {
        $memberId = $this->memberId($user);

        return $memberId !== null && (int) $r->pemberi_referral_id === $memberId;
    }

    private function isPenerima(User $user, Referral $r): bool
    {
        $memberId = $this->memberId($user);

        return $memberId !== null && (int) $r->penerima_referral_id === $memberId;
    }

    private function memberId(User $user): ?int
    {
        $id = $user->member?->id;

        return $id === null ? null : (int) $id;
    }
}
