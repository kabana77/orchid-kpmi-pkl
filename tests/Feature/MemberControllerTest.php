<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Opportunity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_detail_displays_active_opportunities(): void
    {
        $member = Member::query()->create([
            'nama_lengkap' => 'Anggota Uji',
            'status_aktif' => true,
        ]);

        Opportunity::query()->create([
            'member_id' => $member->id,
            'tipe' => Opportunity::OFFER,
            'judul' => 'Peluang Uji',
            'deskripsi' => 'Deskripsi peluang uji',
            'tanggal_expired' => today()->addDays(7),
            'status' => Opportunity::PUBLISHED,
        ]);

        $response = $this->get(route('public.anggota.show', $member));

        $response->assertOk();
        $response->assertSee('Peluang Uji');
    }
}
