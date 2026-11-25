<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            // user_id sengaja tidak diisi: anggota contoh belum punya akun login.
            // foto, linkedin, website, instagram juga dibiarkan kosong (nullable).
            'nama_lengkap' => fake()->name(),
            'nomor_wa'     => '08' . fake()->numerify('#########'),
            'email'        => fake()->unique()->safeEmail(),
            'kota'         => fake()->randomElement([
                'Pemalang', 'Tegal', 'Pekalongan', 'Semarang', 'Jakarta', 'Bandung', 'Surabaya',
            ]),
            'bio'          => fake()->sentence(12),
            'status_aktif' => true,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['status_aktif' => false]);
    }
}