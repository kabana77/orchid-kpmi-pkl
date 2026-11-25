<?php

namespace Tests\Feature;

use App\Models\Referral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_referral_factory_creates_two_named_members(): void
    {
        $referral = Referral::factory()->create();

        $this->assertModelExists($referral);
        $this->assertNotSame($referral->pemberi_referral_id, $referral->penerima_referral_id);
        $this->assertNotSame('', $referral->pemberi->nama_lengkap);
        $this->assertNotSame('', $referral->penerima->nama_lengkap);
    }
}
