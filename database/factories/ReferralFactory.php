<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Referral;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Referral>
 */
class ReferralFactory extends Factory
{
    protected $model = Referral::class;

    public function definition(): array
    {
        return [
            // Dua Member::factory() terpisah, jadi pemberi dan penerima pasti orang berbeda.
            'pemberi_referral_id'  => Member::factory(),
            'penerima_referral_id' => Member::factory(),
            'client_name'          => fake()->name(),
            'project_name'         => fake()->randomElement([
                'Website toko online', 'Branding UMKM', 'Pembukuan bulanan',
                'Aplikasi kasir', 'Kontrak kerja sama supplier', 'Foto produk katalog',
            ]),
            'nilai_estimasi'       => fake()->boolean(70) ? fake()->numberBetween(2, 200) * 500_000 : null,
            'status'               => Referral::INTRODUCED,
            'status_diubah_at'     => now(),
            'catatan'              => fake()->boolean(50) ? fake()->sentence() : null,
        ];
    }

    /** Untuk uji dengan tokoh tetap, misalnya Ani dan Budi. */
    public function between(Member $pemberi, Member $penerima): static
    {
        return $this->state(fn () => [
            'pemberi_referral_id'  => $pemberi->id,
            'penerima_referral_id' => $penerima->id,
        ]);
    }

    public function followUp(): static
    {
        return $this->withStatus(Referral::FOLLOW_UP);
    }

    public function negotiation(): static
    {
        return $this->withStatus(Referral::NEGOTIATION);
    }

    public function won(): static
    {
        return $this->withStatus(Referral::WON);
    }

    public function lost(): static
    {
        return $this->withStatus(Referral::LOST);
    }

    private function withStatus(string $status): static
    {
        return $this->state(fn () => [
            'status'           => $status,
            'status_diubah_at' => now(),
        ]);
    }
}
