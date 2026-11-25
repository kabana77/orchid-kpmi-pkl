<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Referral extends Model
{
    use AsSource, Filterable, HasFactory, SoftDeletes;

    public const INTRODUCED  = 'introduced';
    public const FOLLOW_UP   = 'follow_up';
    public const NEGOTIATION = 'negotiation';
    public const WON         = 'won';
    public const LOST        = 'lost';

    public const LABELS = [
        self::INTRODUCED  => 'Introduced',
        self::FOLLOW_UP   => 'Follow Up',
        self::NEGOTIATION => 'Negotiation',
        self::WON         => 'Won',
        self::LOST        => 'Lost',
    ];

    /**
     * Urutan status yang diizinkan. Won dan Lost final (daftar kosong);
     * membukanya kembali hanya lewat ReferralService::reopen() oleh Admin.
     */
    public const TRANSITIONS = [
        self::INTRODUCED  => [self::FOLLOW_UP, self::LOST],
        self::FOLLOW_UP   => [self::NEGOTIATION, self::LOST],
        self::NEGOTIATION => [self::WON, self::LOST],
        self::WON         => [],
        self::LOST        => [],
    ];

    /**
     * Sengaja TANPA status, status_diubah_at, dan pemberi_referral_id.
     * Ketiganya hanya boleh diubah oleh ReferralService.
     */
    protected $fillable = [
        'penerima_referral_id', 'client_name', 'project_name', 'nilai_estimasi', 'catatan',
    ];

    protected $casts = [
        'nilai_estimasi'   => 'integer',
        'status_diubah_at' => 'datetime',
    ];

    // Kolom yang boleh difilter dari tabel (Orchid)
    protected $allowedFilters = [
        'client_name'  => Like::class,
        'project_name' => Like::class,
        'status'       => Where::class,
    ];

    // Kolom yang boleh di-sort dari tabel (Orchid)
    protected $allowedSorts = [
        'id', 'client_name', 'project_name', 'nilai_estimasi', 'status', 'status_diubah_at', 'created_at',
    ];

    // withTrashed: riwayat tetap utuh walau anggota sudah di-soft-delete
    public function pemberi(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'pemberi_referral_id')->withTrashed();
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'penerima_referral_id')->withTrashed();
    }

    /**
     * Referral yang melibatkan anggota ini, sebagai pemberi ATAU penerima.
     * Dikelompokkan dalam closure supaya orWhere tidak merusak where lain
     * (misalnya filter status) yang dirangkai setelahnya.
     */
    public function scopeInvolving(Builder $query, int $memberId): Builder
    {
        return $query->where(function (Builder $q) use ($memberId) {
            $q->where('pemberi_referral_id', $memberId)
              ->orWhere('penerima_referral_id', $memberId);
        });
    }

    public function scopeWon(Builder $query): Builder
    {
        return $query->where('status', self::WON);
    }

    public function allowedNextStatuses(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    public function canMoveTo(string $status): bool
    {
        return in_array($status, $this->allowedNextStatuses(), true);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [self::WON, self::LOST], true);
    }

    public function statusLabel(): string
    {
        return self::LABELS[$this->status] ?? (string) $this->status;
    }
}
