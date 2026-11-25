<?php

/*
 * Uji manual Modul 6 (Policy + Service) lintas role.
 * AMAN: semua data uji dibungkus transaksi dan di-rollback di akhir.
 *
 * Jalankan:  php artisan tinker scripts/referral_check.php
 * Lalu ketik exit untuk keluar dari tinker.
 */

use App\Models\Member;
use App\Models\Referral;
use App\Models\User;
use App\Services\Referral\ReferralService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

$passed = 0;
$failed = 0;

$check = function (string $label, $actual, $expected) use (&$passed, &$failed) {
    $ok = $actual === $expected;
    $ok ? $passed++ : $failed++;
    echo ($ok ? '  PASS  ' : '  FAIL  ') . $label
        . ($ok ? '' : '  (dapat: ' . var_export($actual, true) . ', seharusnya: ' . var_export($expected, true) . ')')
        . PHP_EOL;
};

$throws = function (callable $fn): bool {
    try {
        $fn();

        return false;
    } catch (\DomainException $e) {
        return true;
    }
};

$can = fn (User $u, string $ability, $target): bool => Gate::forUser($u)->allows($ability, $target);

$makeUser = function (string $name, array $permissions, bool $withMember = true) {
    $user = new User();
    $user->forceFill([
        'name'        => $name,
        'email'       => strtolower($name) . '.' . uniqid() . '@referral-check.test',
        'password'    => bcrypt('password'),
        'permissions' => $permissions,
    ])->save();

    if ($withMember) {
        Member::factory()->create(['user_id' => $user->id, 'nama_lengkap' => $name]);
    }

    return $user->fresh();
};

$payload = fn (int $penerimaId, array $extra = []) => array_merge([
    'penerima_referral_id' => $penerimaId,
    'client_name'          => 'Pak Joko',
    'project_name'         => 'Website toko',
    'nilai_estimasi'       => 15000000,
], $extra);

DB::beginTransaction();

try {
    $svc        = app(ReferralService::class);
    $memberPerm = ['platform.my.referrals' => true];

    $ani   = $makeUser('Ani', $memberPerm);
    $budi  = $makeUser('Budi', $memberPerm);
    $citra = $makeUser('Citra', $memberPerm);
    $admin = $makeUser('Admin', ['platform.referrals' => true], false); // admin murni, tanpa Member
    $dodi  = $makeUser('Dodi', []);                                      // punya Member, tanpa izin referral
    $eka   = Member::factory()->nonaktif()->create();

    echo PHP_EOL . '== Siapa boleh membuat ==' . PHP_EOL;
    $check('Ani boleh membuat', $can($ani, 'create', Referral::class), true);
    $check('Admin boleh membuat', $can($admin, 'create', Referral::class), true);
    $check('Dodi (tanpa izin referral) tidak boleh', $can($dodi, 'create', Referral::class), false);

    echo PHP_EOL . '== Aturan pembuatan (service) ==' . PHP_EOL;
    // Ani mencoba memalsukan pemberi menjadi Citra.
    $r = $svc->create($ani, $payload($budi->member->id, ['pemberi_referral_id' => $citra->member->id]));
    $check('Pemberi dipaksa = Ani (pemberi palsu diabaikan)', $r->pemberi_referral_id, $ani->member->id);
    $check('Status awal = introduced', $r->status, Referral::INTRODUCED);
    $check('status_diubah_at terisi', $r->status_diubah_at !== null, true);
    $check('Ani -> Ani ditolak', $throws(fn () => $svc->create($ani, $payload($ani->member->id))), true);
    $check('Penerima nonaktif ditolak', $throws(fn () => $svc->create($ani, $payload($eka->id))), true);
    $check('Admin murni tanpa pemberi ditolak', $throws(fn () => $svc->create($admin, $payload($budi->member->id))), true);
    $byAdmin = $svc->create($admin, $payload($budi->member->id, ['pemberi_referral_id' => $citra->member->id]));
    $check('Admin boleh memilih pemberi', $byAdmin->pemberi_referral_id, $citra->member->id);

    echo PHP_EOL . '== Referral Ani -> Budi, status Introduced ==' . PHP_EOL;
    $check('view: Ani', $can($ani, 'view', $r), true);
    $check('view: Budi', $can($budi, 'view', $r), true);
    $check('view: Citra (bukan pihak terkait)', $can($citra, 'view', $r), false);
    $check('view: Admin', $can($admin, 'view', $r), true);
    $check('update data: Ani (masih Introduced)', $can($ani, 'update', $r), true);
    $check('update data: Budi', $can($budi, 'update', $r), false);
    $check('update data: Citra', $can($citra, 'update', $r), false);
    $check('update data: Admin', $can($admin, 'update', $r), true);
    $check('ubah status: Budi', $can($budi, 'changeStatus', $r), true);
    $check('ubah status: Ani', $can($ani, 'changeStatus', $r), false);
    $check('ubah status: Citra', $can($citra, 'changeStatus', $r), false);
    $check('ubah status: Admin', $can($admin, 'changeStatus', $r), true);
    $check('hapus: Ani', $can($ani, 'delete', $r), false);
    $check('hapus: Budi', $can($budi, 'delete', $r), false);
    $check('hapus: Admin', $can($admin, 'delete', $r), true);
    $check('reopen: Admin (belum final)', $can($admin, 'reopen', $r), false);

    echo PHP_EOL . '== Perpindahan status dan data basi ==' . PHP_EOL;
    $stale = Referral::find($r->id); // salinan "form Ani" yang dibuka sebelum Budi bertindak
    $check('Introduced -> Won langsung ditolak', $throws(fn () => $svc->changeStatus($r, Referral::WON)), true);
    $r = $svc->changeStatus($r, Referral::FOLLOW_UP);
    $check('Introduced -> Follow Up berhasil', $r->status, Referral::FOLLOW_UP);
    $check('Ani edit lewat data basi ditolak service', $throws(fn () => $svc->update($ani, $stale, ['client_name' => 'Diubah Ani'])), true);
    $check('update data: Ani (sudah Follow Up)', $can($ani, 'update', $r), false);
    $r = $svc->update($admin, $r, ['catatan' => 'dikoreksi admin']);
    $check('Admin tetap bisa edit setelah Follow Up', $r->catatan, 'dikoreksi admin');

    $r = $svc->changeStatus($r, Referral::NEGOTIATION);
    $r = $svc->changeStatus($r, Referral::WON);
    $check('Negotiation -> Won berhasil', $r->status, Referral::WON);

    echo PHP_EOL . '== Won itu final, hanya admin yang membuka ==' . PHP_EOL;
    $check('ubah status: Budi (sudah Won)', $can($budi, 'changeStatus', $r), false);
    $check('ubah status: Admin (sudah Won)', $can($admin, 'changeStatus', $r), false);
    $check('Won -> Lost lewat changeStatus ditolak', $throws(fn () => $svc->changeStatus($r, Referral::LOST)), true);
    $check('reopen: Admin', $can($admin, 'reopen', $r), true);
    $check('reopen: Budi', $can($budi, 'reopen', $r), false);
    $check('reopen: Ani', $can($ani, 'reopen', $r), false);
    $check('reopen ke Won/Lost ditolak', $throws(fn () => $svc->reopen($r, Referral::LOST)), true);
    $r = $svc->reopen($r, Referral::NEGOTIATION);
    $check('Admin reopen -> Negotiation berhasil', $r->status, Referral::NEGOTIATION);
    $check('reopen saat belum final ditolak', $throws(fn () => $svc->reopen($r, Referral::FOLLOW_UP)), true);
} finally {
    DB::rollBack();
    echo PHP_EOL . "Lolos: $passed   Gagal: $failed" . PHP_EOL;
    echo 'Semua data uji sudah di-rollback. (Nomor auto-increment di MySQL tetap maju, itu normal.)' . PHP_EOL;
}
