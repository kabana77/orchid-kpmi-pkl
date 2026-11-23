<?php

namespace App\Support;

final class PublicUrl
{
    /**
     * Tautan dari input anggota: hanya http/https yang lolos (menolak javascript:, data:, dll).
     * Jika $handleBase diberikan, nilai berupa username (mis. "@andi") diubah menjadi URL profil.
     */
    public static function normalize(?string $value, ?string $handleBase = null, ?string $handlePattern = null): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $isHttp = preg_match('#^https?://#i', $value) === 1;

        if (! $isHttp && $handleBase !== null && $handlePattern !== null) {
            $handle = ltrim($value, '@');

            if (preg_match($handlePattern, $handle) === 1) {
                return $handleBase.$handle;
            }
        }

        if (! $isHttp && preg_match('/^[a-z][a-z0-9+.\-]*:/i', $value) === 1) {
            return null;
        }

        $url = $isHttp ? $value : 'https://'.$value;

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true) ? $url : null;
    }
}
