<?php

namespace App\Models\Concerns;

use App\Support\PublicUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

/**
 * Tambahkan ke model Business:  use HasPublicBusiness;
 * Scope aktif() yang sudah ada di Business tetap dipakai.
 */
trait HasPublicBusiness
{
    /** Hanya usaha milik anggota yang masih aktif dan belum dihapus. */
    public function scopeDariAnggotaAktif(Builder $query): Builder
    {
        return $query->whereHas('member', fn (Builder $m) => $m->where('status_aktif', true));
    }

    /**
     * Filter direktori usaha. Kunci: q (nama usaha), kategori, status (aktif | nonaktif | semua).
     * Tanpa status, yang tampil hanya usaha Aktif.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $status = $filters['status'] ?? 'aktif';

        return $query
            ->when($filters['q'] ?? null, function (Builder $q, string $value) {
                $value = addcslashes($value, '%_\\');
                $q->where('nama_usaha', 'like', "%{$value}%");
            })
            ->when($filters['kategori'] ?? null, fn (Builder $q, string $value) => $q->where('kategori', $value))
            ->when($status === 'aktif', fn (Builder $q) => $q->aktif())
            ->when($status === 'nonaktif', fn (Builder $q) => $q->where('status', '!=', 'aktif'));
    }

    protected function inisial(): Attribute
    {
        return Attribute::get(fn () => Str::of((string) $this->nama_usaha)
            ->squish()
            ->explode(' ')
            ->take(2)
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode(''));
    }

    protected function isAktif(): Attribute
    {
        return Attribute::get(fn () => strtolower((string) $this->status) === 'aktif');
    }

    protected function websiteUrl(): Attribute
    {
        return Attribute::get(fn () => PublicUrl::normalize($this->website));
    }
}
