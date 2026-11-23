<?php

namespace App\Models\Concerns;

use App\Support\PublicUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Tambahkan ke model Member:  use HasPublicDirectory;
 */
trait HasPublicDirectory
{
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Filter direktori publik. Kunci: q (nama), kota, skill (id), peran (array id).
     * Beberapa peran berarti "salah satu dari peran itu".
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $q, string $value) {
                $value = addcslashes($value, '%_\\');
                $q->where('nama_lengkap', 'like', "%{$value}%");
            })
            ->when($filters['kota'] ?? null, fn (Builder $q, string $value) => $q->where('kota', $value))
            ->when($filters['skill'] ?? null, fn (Builder $q, $value) => $q->whereHas('skills', fn ($s) => $s->whereKey($value)))
            ->when($filters['peran'] ?? null, fn (Builder $q, array $value) => $q->whereHas('professionalRoles', fn ($r) => $r->whereKey($value)));
    }

    protected function inisial(): Attribute
    {
        return Attribute::get(fn () => Str::of((string) $this->nama_lengkap)
            ->squish()
            ->explode(' ')
            ->take(2)
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode(''));
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::get(function () {
            $foto = $this->foto;

            if (! $foto) {
                return null;
            }

            return Str::startsWith($foto, ['http://', 'https://', '/'])
                ? $foto
                : Storage::disk('public')->url($foto);
        });
    }

    protected function websiteUrl(): Attribute
    {
        return Attribute::get(fn () => PublicUrl::normalize($this->website));
    }

    protected function linkedinUrl(): Attribute
    {
        return Attribute::get(fn () => PublicUrl::normalize($this->linkedin, 'https://www.linkedin.com/in/', '/^[A-Za-z0-9-]{3,100}$/'));
    }

    protected function instagramUrl(): Attribute
    {
        return Attribute::get(fn () => PublicUrl::normalize($this->instagram, 'https://www.instagram.com/', '/^[A-Za-z0-9._]{1,30}$/'));
    }

    /** Nomor WA ke format wa.me (0812... atau 812... menjadi 62812...). */
    protected function waUrl(): Attribute
    {
        return Attribute::get(function () {
            $digits = preg_replace('/\D+/', '', (string) $this->nomor_wa);

            if ($digits === '') {
                return null;
            }

            if (str_starts_with($digits, '0')) {
                $digits = '62'.substr($digits, 1);
            } elseif (str_starts_with($digits, '8')) {
                $digits = '62'.$digits;
            }

            return 'https://wa.me/'.$digits;
        });
    }
}
