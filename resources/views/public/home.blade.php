@extends('layouts.public')

@section('title', 'KPMI Pekalongan · Komunitas pelaku usaha dan profesional')
@section('description', 'Temukan rekan usaha, bangun kolaborasi, dan saling merekomendasikan klien di komunitas KPMI Pekalongan.')

@section('content')
<section class="kp-hero" aria-labelledby="judul-beranda">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 d-flex flex-column gap-3">
                <p class="kp-hero-kicker mb-0">Wadah bagi para pelaku usaha dan profesional</p>
                <h1 id="judul-beranda">Tumbuh bersama jejaring usaha dan profesional lokal</h1>
                <p class="kp-hero-lead mb-0">Temukan rekan usaha, bangun kolaborasi, dan saling merekomendasikan klien di satu tempat.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <a class="btn btn-kp-gold" href="{{ route('public.anggota.index') }}">Lihat Direktori Anggota</a>
                    <a class="btn btn-kp-light" href="{{ route('public.usaha.index') }}">Direktori Usaha</a>
                </div>
            </div>

            <div class="col-lg-6">
                <section class="kp-board" aria-labelledby="papan-peluang">
                    <h2 id="papan-peluang">Papan Peluang</h2>
                    @forelse ($recentOpportunities as $opportunity)
                        <a class="kp-board-item" href="{{ route('public.peluang.show', $opportunity) }}">
                            <span class="kp-board-type">{{ $opportunity->tipe_label }}</span>
                            <div class="kp-board-title">
                                {{ $opportunity->judul }}
                                @if ($opportunity->lokasi)
                                    <br><span class="kp-board-meta">{{ $opportunity->lokasi }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="kp-board-item">
                            <span class="kp-board-meta">Belum ada peluang aktif.</span>
                        </div>
                    @endforelse
                    <a class="kp-board-more" href="{{ route('public.peluang.index') }}">Lihat semua peluang</a>
                </section>
            </div>
        </div>
    </div>
</section>

<div class="container kp-stats">
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col">
            <div class="kp-card h-100">
                <div class="kp-stat-num">{{ number_format($stats['anggota'], 0, ',', '.') }}</div>
                <div class="kp-mut">Anggota aktif</div>
            </div>
        </div>
        <div class="col">
            <div class="kp-card h-100">
                <div class="kp-stat-num">{{ number_format($stats['usaha'], 0, ',', '.') }}</div>
                <div class="kp-mut">Usaha aktif</div>
            </div>
        </div>
        <div class="col">
            <div class="kp-card h-100">
                <div class="kp-stat-num">{{ number_format($stats['peluang'], 0, ',', '.') }}</div>
                <div class="kp-mut">Peluang aktif</div>
            </div>
        </div>
    </div>
</div>

@if ($latestMembers->isNotEmpty())
    <section class="container py-5" aria-labelledby="anggota-terbaru">
        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-3">
            <h2 id="anggota-terbaru" class="h3 mb-0">Anggota terbaru</h2>
            <a class="fw-semibold text-decoration-none" href="{{ route('public.anggota.index') }}">Lihat semua anggota</a>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            @foreach ($latestMembers as $member)
                <div class="col">
                    <x-public.member-card :member="$member" />
                </div>
            @endforeach
        </div>
    </section>
@endif

{{-- Seksi "Event mendatang" dan counter "Referral Won" dipasang setelah Modul 6 dan 7+8 selesai. --}}

@guest
    <section class="container pb-5" aria-labelledby="ajakan">
        <div class="kp-card text-center p-4 p-md-5">
            <h2 id="ajakan" class="h3">Ingin bergabung?</h2>
            <p class="kp-mut mx-auto" style="max-width:36rem">Masuk jika sudah terdaftar, atau hubungi pengurus untuk mendapatkan akun anggota.</p>
            <div><a class="btn btn-kp" href="{{ route('platform.login') }}">Masuk</a></div>
        </div>
    </section>
@endguest
@endsection
