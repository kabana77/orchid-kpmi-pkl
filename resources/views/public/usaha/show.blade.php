@extends('layouts.public')

@section('title', $business->nama_usaha.' · KPMI Pekalongan')
@section('description', Str::limit(strip_tags((string) $business->deskripsi), 150) ?: 'Profil usaha anggota KPMI Pekalongan.')

@section('content')
@php
    $profilUrl = route('public.anggota.show', $member);
@endphp

<div class="container pt-4 pb-5">
    <nav class="small" aria-label="Breadcrumb">
        <a class="kp-mut text-decoration-none" href="{{ route('public.home') }}">Beranda</a>
        <span class="kp-mut">/</span>
        <a class="kp-mut text-decoration-none" href="{{ route('public.usaha.index') }}">Usaha</a>
        <span class="kp-mut">/</span>
        <span aria-current="page">{{ $business->nama_usaha }}</span>
    </nav>

    <header class="kp-card d-flex flex-wrap align-items-center gap-4 mt-3 p-4">
        <div class="kp-lg kp-lg-xl" aria-hidden="true">{{ $business->inisial }}</div>

        <div class="flex-grow-1">
            <h1 class="h2 mb-1">{{ $business->nama_usaha }}</h1>
            <div class="kp-mut mb-2">Milik <a href="{{ $profilUrl }}">{{ $member->nama_lengkap }}</a></div>
            <div>
                @if ($business->kategori)
                    <span class="kp-tag kp-tag-s me-1">{{ $business->kategori }}</span>
                @endif
                <span class="kp-bd {{ $business->is_aktif ? 'kp-bd-a' : 'kp-bd-n' }}">{{ $business->is_aktif ? 'Aktif' : 'Non Aktif' }}</span>
            </div>
        </div>

        @if ($business->website_url)
            <a class="btn btn-kp" href="{{ $business->website_url }}" target="_blank" rel="noopener noreferrer nofollow">Kunjungi website</a>
        @endif
    </header>

    <div class="row g-4 mt-1 align-items-start">
        <div class="col-lg-8 d-flex flex-column gap-4">
            @if (filled($business->deskripsi))
                <section class="kp-card" aria-labelledby="tentang">
                    <h2 id="tentang" class="h5 mb-2">Tentang usaha</h2>
                    <p class="mb-0" style="line-height:1.65">{!! nl2br(e($business->deskripsi)) !!}</p>
                </section>
            @endif

            <section class="kp-card" aria-labelledby="info">
                <h2 id="info" class="h5 mb-3">Informasi usaha</h2>
                <dl class="row mb-0 gy-2">
                    @if ($business->kategori)
                        <dt class="col-sm-4 kp-mut fw-normal">Kategori</dt>
                        <dd class="col-sm-8 mb-0">{{ $business->kategori }}</dd>
                    @endif
                    @if ($business->alamat)
                        <dt class="col-sm-4 kp-mut fw-normal">Alamat</dt>
                        <dd class="col-sm-8 mb-0">{{ $business->alamat }}</dd>
                    @endif
                    @if ($business->website_url)
                        <dt class="col-sm-4 kp-mut fw-normal">Website</dt>
                        <dd class="col-sm-8 mb-0">
                            <a href="{{ $business->website_url }}" target="_blank" rel="noopener noreferrer nofollow">{{ $business->website }}</a>
                        </dd>
                    @endif
                    <dt class="col-sm-4 kp-mut fw-normal">Status</dt>
                    <dd class="col-sm-8 mb-0">
                        <span class="kp-bd {{ $business->is_aktif ? 'kp-bd-a' : 'kp-bd-n' }}">{{ $business->is_aktif ? 'Aktif' : 'Non Aktif' }}</span>
                    </dd>
                </dl>
            </section>

            @if ($opportunities->isNotEmpty())
                <section aria-labelledby="peluang">
                    <h2 id="peluang" class="h4 mb-3">Peluang dari pemilik</h2>
                    <div class="d-flex flex-column gap-3">
                        @foreach ($opportunities as $opportunity)
                            <x-public.opportunity-card :opportunity="$opportunity" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="col-lg-4 d-flex flex-column gap-4">
            <section class="kp-card d-flex flex-column gap-2" aria-labelledby="pemilik">
                <h2 id="pemilik" class="h5 mb-2">Pemilik usaha</h2>
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

            @if ($otherBusinesses->isNotEmpty())
                <section class="kp-card" aria-labelledby="lain">
                    <h2 id="lain" class="h5 mb-3">Usaha lain dari pemilik</h2>
                    <div class="d-flex flex-column gap-3">
                        @foreach ($otherBusinesses as $other)
                            <a class="d-flex align-items-center gap-3 text-decoration-none" href="{{ route('public.usaha.show', $other) }}">
                                <div class="kp-lg" style="width:44px;height:44px;font-size:.95rem" aria-hidden="true">{{ $other->inisial }}</div>
                                <div>
                                    <div class="fw-semibold" style="color:var(--kp-navy)">{{ $other->nama_usaha }}</div>
                                    <span class="kp-bd {{ $other->is_aktif ? 'kp-bd-a' : 'kp-bd-n' }}">{{ $other->is_aktif ? 'Aktif' : 'Non Aktif' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection
