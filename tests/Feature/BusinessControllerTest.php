<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Member;
use App\Models\Opportunity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_directory_displays_businesses_of_active_members(): void
    {
        $activeMember = Member::query()->create([
            'nama_lengkap' => 'Anggota Aktif',
            'status_aktif' => true,
        ]);
        $inactiveMember = Member::query()->create([
            'nama_lengkap' => 'Anggota Nonaktif',
            'status_aktif' => false,
        ]);

        Business::query()->create([
            'member_id' => $activeMember->id,
            'nama_usaha' => 'Usaha Anggota Aktif',
            'kategori' => 'Kuliner',
            'status' => Business::AKTIF,
        ]);
        Business::query()->create([
            'member_id' => $inactiveMember->id,
            'nama_usaha' => 'Usaha Anggota Nonaktif',
            'kategori' => 'Kuliner',
            'status' => Business::AKTIF,
        ]);

        $response = $this->get(route('public.usaha.index'));

        $response->assertOk();
        $response->assertSee('Usaha Anggota Aktif');
        $response->assertDontSee('Usaha Anggota Nonaktif');
    }

    public function test_business_detail_displays_only_published_unexpired_opportunities(): void
    {
        $member = Member::query()->create([
            'nama_lengkap' => 'Anggota Aktif',
            'status_aktif' => true,
        ]);
        $business = Business::query()->create([
            'member_id' => $member->id,
            'nama_usaha' => 'Usaha Anggota Aktif',
            'kategori' => 'Kuliner',
            'status' => Business::AKTIF,
        ]);

        Opportunity::query()->create([
            'member_id' => $member->id,
            'tipe' => Opportunity::OFFER,
            'judul' => 'Peluang Terbit',
            'deskripsi' => 'Deskripsi peluang terbit',
            'tanggal_expired' => today()->addDays(7),
            'status' => Opportunity::PUBLISHED,
        ]);
        Opportunity::query()->create([
            'member_id' => $member->id,
            'tipe' => Opportunity::OFFER,
            'judul' => 'Peluang Draft',
            'deskripsi' => 'Deskripsi peluang draft',
            'tanggal_expired' => today()->addDays(7),
            'status' => Opportunity::DRAFT,
        ]);
        Opportunity::query()->create([
            'member_id' => $member->id,
            'tipe' => Opportunity::OFFER,
            'judul' => 'Peluang Kedaluwarsa',
            'deskripsi' => 'Deskripsi peluang kedaluwarsa',
            'tanggal_expired' => today()->subDay(),
            'status' => Opportunity::PUBLISHED,
        ]);

        $response = $this->get(route('public.usaha.show', $business));

        $response->assertOk();
        $response->assertSee('Peluang Terbit');
        $response->assertDontSee('Peluang Draft');
        $response->assertDontSee('Peluang Kedaluwarsa');
    }
}
