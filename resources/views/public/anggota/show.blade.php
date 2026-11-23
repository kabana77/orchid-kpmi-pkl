@extends('layouts.public')

@section('title', $member->nama_lengkap.' · KPMI Pekalongan')
@section('description', Str::limit(strip_tags((string) $member->bio), 150) ?: 'Profil anggota KPMI Pekalongan.')

@section('content')
@php
    $sejak = $member->created_at?->locale('id')->translatedFormat('F Y');
    $links = array_filter([
        'LinkedIn' => $member->linkedin_url,
        'Website' => $member->website_url,
        'Instagram' => $member->instagram_url,
    ]);
@endphp

<div class="container pt-4 pb-5">
    <nav class="small" aria-label="Breadcrumb">
        <a class="kp-mut text-decoration-none" href="{{ route('public.home') }}">Beranda</a>
        <span class="kp-mut">/</span>
        <a class="kp-mut text-decoration-none" href="{{ route('public.anggota.index') }}">Anggota</a>
        <span class="kp-mut">/</span>
        <span aria-current="page">{{ $member->nama_lengkap }}</span>
    </nav>

    <header class="kp-card d-flex flex-wrap align-items-center gap-4 mt-3 p-4">
        @if ($member->foto_url)
            <img class="kp-av kp-av-lg" src="{{ $member->foto_url }}" alt="Foto {{ $member->nama_lengkap }}" width="96" height="96">
        @else
            <div class="kp-av kp-av-lg" aria-hidden="true">{{ $member->inisial }}</div>
        @endif

        <div class="flex-grow-1">
            <h1 class="h2 mb-1">{{ $member->nama_lengkap }}</h1>
            <div class="kp-mut mb-2">
                {{ collect([$member->kota, $sejak ? 'Anggota sejak '.$sejak : null])->filter()->implode(' · ') }}
            </div>

            @if ($member->professionalRoles->isNotEmpty())
                <div>
                    @foreach ($member->professionalRoles as $role)
                        <span class="kp-tag me-1 mb-1">{{ $role->nama }}</span>
                    @endforeach
                </div>
            @endif

            @if ($links)
                <div class="d-flex flex-wrap gap-3 small mt-2">
                    @foreach ($links as $label => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer nofollow">{{ $label }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </header>

    <div class="row g-4 mt-1 align-items-start">
        <div class="col-lg-8 d-flex flex-column gap-4">
            @if (filled($member->bio))
                <section class="kp-card" aria-labelledby="tentang">
                    <h2 id="tentang" class="h5 mb-2">Tentang</h2>
                    <p class="mb-0" style="line-height:1.65">{!! nl2br(e($member->bio)) !!}</p>
                </section>
            @endif

            @if ($businesses->isNotEmpty())
                <section aria-labelledby="usaha">
                    <h2 id="usaha" class="h4 mb-3">Usaha</h2>
                    <div class="d-flex flex-column gap-3">
                        @foreach ($businesses as $business)
                            <x-public.business-card :business="$business" />
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($opportunities->isNotEmpty())
                <section aria-labelledby="peluang">
                    <h2 id="peluang" class="h4 mb-3">Peluang aktif</h2>
                    <div class="d-flex flex-column gap-3">
                        @foreach ($opportunities as $opportunity)
                            <x-public.opportunity-card :opportunity="$opportunity" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="col-lg-4 d-flex flex-column gap-4">
            <section class="kp-card" aria-labelledby="skill">
                <h2 id="skill" class="h5 mb-3">Skill</h2>
                @forelse ($member->skills as $skill)
                    <span class="kp-tag kp-tag-s me-1 mb-1">{{ $skill->nama }}</span>
                @empty
                    <p class="kp-mut small mb-0">Belum ada skill yang dicantumkan.</p>
                @endforelse
            </section>

            <section class="kp-card d-flex flex-column gap-2" aria-labelledby="kontak">
                @auth
                    <h2 id="kontak" class="h5 mb-1">Kontak</h2>
                    @if ($member->wa_url)
                        <a class="btn btn-kp" href="{{ $member->wa_url }}" target="_blank" rel="noopener noreferrer">
                            WhatsApp {{ $member->nomor_wa }}
                        </a>
                    @endif
                    @if ($member->email)
                        <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                    @endif
                    @if (! $member->wa_url && ! $member->email)
                        <p class="kp-mut small mb-0">Anggota ini belum mencantumkan kontak.</p>
                    @endif
                @else
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#14213D" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>
                    </svg>
                    <h2 id="kontak" class="h5 mb-0">Kontak hanya untuk anggota</h2>
                    <p class="kp-mut small mb-2">Masuk untuk melihat nomor WhatsApp dan email {{ Str::of($member->nama_lengkap)->before(' ') }}.</p>
                    <a class="btn btn-kp" href="{{ route('platform.login') }}">Masuk</a>
                @endauth
            </section>
        </aside>
    </div>
</div>
@endsection
