<?php

namespace App\Models\Concerns;

use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Tambahkan ke model Opportunity:  use HasPublicOpportunity;
 */
trait HasPublicOpportunity
{
    /** Pemasang peluang. Dinamai "pemasang" agar tidak bentrok dengan relasi member() yang mungkin sudah ada. */
    public function pemasang(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /** Peluang berstatus Published dan belum lewat tanggal_expired. */
    public function scopeTerbit(Builder $query): Builder
    {
        return $query
            ->whereRaw('LOWER(status) = ?', ['published'])
            ->where(function (Builder $q) {
                $q->whereNull('tanggal_expired')
                    ->orWhereDate('tanggal_expired', '>=', now()->toDateString());
            });
    }

    /** Hanya peluang milik anggota yang masih aktif dan belum dihapus. */
    public function scopeDariAnggotaAktif(Builder $query): Builder
    {
        return $query->whereIn('member_id', Member::query()->aktif()->select('id'));
    }

    /** Filter papan peluang publik. Kunci: q (judul), tipe (need | offer | collaborate). */
    public function scopeFilterPublik(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $q, string $value) {
                $value = addcslashes($value, '%_\\');
                $q->where('judul', 'like', "%{$value}%");
            })
            ->when($filters['tipe'] ?? null, fn (Builder $q, string $value) => $q->whereRaw('LOWER(tipe) = ?', [strtolower($value)]));
    }

    protected function isTerbuka(): Attribute
    {
        return Attribute::get(function () {
            if (strtolower((string) $this->status) !== 'published') {
                return false;
            }

            return blank($this->tanggal_expired)
                || Carbon::parse($this->tanggal_expired)->endOfDay()->isFuture();
        });
    }

    protected function tipeLabel(): Attribute
    {
        return Attribute::get(fn () => Str::headline((string) $this->tipe));
    }

    protected function batasLabel(): Attribute
    {
        return Attribute::get(fn () => blank($this->tanggal_expired)
            ? null
            : Carbon::parse($this->tanggal_expired)->locale('id')->translatedFormat('j M Y'));
    }

    protected function terbitLabel(): Attribute
    {
        return Attribute::get(fn () => $this->created_at?->copy()->locale('id')->translatedFormat('j M Y'));
    }
}
