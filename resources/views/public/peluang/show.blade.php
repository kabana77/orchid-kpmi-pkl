@extends('layouts.public')

@section('title', $opportunity->judul.' · Papan Peluang KPMI Pekalongan')
@section('description', Str::limit(strip_tags((string) $opportunity->deskripsi), 150) ?: 'Peluang dari anggota KPMI Pekalongan.')

@php
    $terbuka = $opportunity->is_terbuka;
    $profilUrl = route('public.anggota.show', $member);
    $meta = array_filter([
        $opportunity->lokasi,
        $opportunity->batas_label ? 'Berlaku hingga '.$opportunity->batas_label : null,
    ]);
@endphp

@unless ($terbuka)
    @push('head')
        <meta name="robots" content="noindex">
    @endpush
@endunless

@section('content')
<div class="container pt-4 pb-5">
    <nav class="small" aria-label="Breadcrumb">
        <a class="kp-mut text-decoration-none" href="{{ route('public.home') }}">Beranda</a>
        <span class="kp-mut">/</span>
        <a class="kp-mut text-decoration-none" href="{{ route('public.peluang.index') }}">Peluang</a>
        <span class="kp-mut">/</span>
        <span aria-current="page">{{ $opportunity->judul }}</span>
    </nav>

    <header class="kp-card d-flex flex-column gap-2 mt-3 p-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="kp-chip">{{ $opportunity->tipe_label }}</span>
            <span class="kp-bd {{ $terbuka ? 'kp-bd-a' : 'kp-bd-n' }}">{{ $terbuka ? 'Terbuka' : 'Ditutup' }}</span>
        </div>
        <h1 class="h2 mb-0">{{ $opportunity->judul }}</h1>
        @if ($meta)
            <div class="kp-mut">{{ implode(' · ', $meta) }}</div>
        @endif
    </header>

    @unless ($terbuka)
        <div class="kp-notice mt-3" role="status">Peluang ini sudah ditutup dan tidak menerima tanggapan baru.</div>
    @endunless

    <div class="row g-4 mt-1 align-items-start">
        <div class="col-lg-8 d-flex flex-column gap-4">
            @if (filled($opportunity->deskripsi))
                <section class="kp-card" aria-labelledby="deskripsi">
                    <h2 id="deskripsi" class="h5 mb-2">Deskripsi</h2>
                    <p class="mb-0" style="line-height:1.65">{!! nl2br(e($opportunity->deskripsi)) !!}</p>
                </section>
            @endif

            <section class="kp-card" aria-labelledby="ringkasan">
                <h2 id="ringkasan" class="h5 mb-3">Ringkasan</h2>
                <dl class="row mb-0 gy-2">
                    <dt class="col-sm-4 kp-mut fw-normal">Tipe</dt>
                    <dd class="col-sm-8 mb-0">{{ $opportunity->tipe_label }}</dd>
                    @if ($opportunity->lokasi)
                        <dt class="col-sm-4 kp-mut fw-normal">Lokasi</dt>
                        <dd class="col-sm-8 mb-0">{{ $opportunity->lokasi }}</dd>
                    @endif
                    @if ($opportunity->batas_label)
                        <dt class="col-sm-4 kp-mut fw-normal">Batas waktu</dt>
                        <dd class="col-sm-8 mb-0">{{ $opportunity->batas_label }}</dd>
                    @endif
                    @if ($opportunity->terbit_label)
                        <dt class="col-sm-4 kp-mut fw-normal">Dipublikasikan</dt>
                        <dd class="col-sm-8 mb-0">{{ $opportunity->terbit_label }}</dd>
                    @endif
                </dl>
            </section>

            @if ($related->isNotEmpty())
                <section aria-labelledby="lain">
                    <h2 id="lain" class="h4 mb-3">Peluang lain</h2>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        @foreach ($related as $item)
                            <div class="col">
                                <x-public.opportunity-board-card :opportunity="$item" />
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="col-lg-4 d-flex flex-column gap-4">
            <section class="kp-card d-flex flex-column gap-2" aria-labelledby="pemasang">
                <h2 id="pemasang" class="h5 mb-2">Diposting oleh</h2>
                <div class="d-flex align-items-center gap-3">
                    @if ($member->foto_url)
                        <img class="kp-av" src="{{ $member->foto_url }}" alt="" width="56" height="56" loading="lazy">
                    @else
                        <div class="kp-av" aria-hidden="true">{{ $member->inisial }}</div>
                    @endif
                    <div>
                        <div class="fw-semibold">{{ $member->nama_lengkap }}</div>
                        @if ($member->kota)
                            <div class="kp-mut small">{{ $member->kota }}</div>
                        @endif
                    </div>
                </div>
                @if ($member->professionalRoles->isNotEmpty())
                    <div>
                        @foreach ($member->professionalRoles as $role)
                            <span class="kp-tag me-1 mb-1">{{ $role->nama }}</span>
                        @endforeach
                    </div>
                @endif
                <a class="btn btn-kp-outline mt-1" href="{{ $profilUrl }}">Lihat profil</a>
            </section>

            @if ($terbuka)
                <section class="kp-card d-flex flex-column gap-2" aria-labelledby="kontak">
                    @auth
                        <h2 id="kontak" class="h5 mb-1">Hubungi pemasang</h2>
                        @if ($member->wa_url)
                            <a class="btn btn-kp" href="{{ $member->wa_url }}" target="_blank" rel="noopener noreferrer">
                                WhatsApp {{ $member->nomor_wa }}
                            </a>
                        @endif
                        @if ($member->email)
                            <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                        @endif
                        @if (! $member->wa_url && ! $member->email)
                            <p class="kp-mut small mb-0">Pemasang belum mencantumkan kontak.</p>
                        @endif
                    @else
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#14213D" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>
                        </svg>
                        <h2 id="kontak" class="h5 mb-0">Kontak hanya untuk anggota</h2>
                        <p class="kp-mut small mb-2">Masuk untuk menghubungi {{ Str::of($member->nama_lengkap)->before(' ') }} lewat WhatsApp atau email.</p>
                        <a class="btn btn-kp" href="{{ route('platform.login') }}">Masuk</a>
                    @endauth
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection
