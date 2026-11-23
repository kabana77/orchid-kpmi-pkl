@extends('layouts.public')

@section('title', 'Papan Peluang · KPMI Pekalongan')

@section('content')
@php
    $tipe = $filters['tipe'] ?? null;
    $q = $filters['q'] ?? null;
    $tabs = [null => 'Semua', 'need' => 'Need', 'offer' => 'Offer', 'collaborate' => 'Collaborate'];
@endphp
<div class="container pt-4 pb-5">
    <div class="kp-mut small">Beranda / Peluang</div>

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mt-2 mb-4">
        <div>
            <h1 class="h2 mb-1">Papan Peluang</h1>
            <p class="kp-mut mb-0">Kebutuhan, penawaran, dan ajakan kolaborasi dari anggota komunitas.</p>
        </div>
        <span class="kp-mut small">
            @if ($opportunities->total())
                Menampilkan {{ $opportunities->firstItem() }} sampai {{ $opportunities->lastItem() }} dari {{ $opportunities->total() }} peluang
            @else
                Tidak ada peluang ditemukan
            @endif
        </span>
    </div>

    <div class="kp-card d-flex flex-wrap gap-3 align-items-end justify-content-between py-3">
        <nav class="d-flex flex-wrap gap-2" aria-label="Tipe peluang">
            @foreach ($tabs as $value => $label)
                <a class="kp-pg @if ($tipe === $value) on @endif"
                   href="{{ route('public.peluang.index', array_filter(['tipe' => $value, 'q' => $q])) }}"
                   @if ($tipe === $value) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('public.peluang.index') }}" style="flex:1 1 260px;max-width:360px">
            @if ($tipe)
                <input type="hidden" name="tipe" value="{{ $tipe }}">
            @endif
            <label class="form-label small kp-mut mb-1" for="q">Cari judul peluang</label>
            <div class="input-group">
                <input id="q" name="q" type="search" class="form-control" maxlength="100"
                       value="{{ $q }}" placeholder="Contoh: desain, supplier">
                <button type="submit" class="btn btn-kp">Cari</button>
            </div>
        </form>
    </div>

    <section class="mt-4" aria-label="Daftar peluang">
        <div class="row row-cols-1 row-cols-lg-2 g-4">
            @forelse ($opportunities as $opportunity)
                <div class="col">
                    <x-public.opportunity-board-card :opportunity="$opportunity" />
                </div>
            @empty
                <div class="col-12">
                    <div class="kp-card text-center py-5">
                        <h2 class="h5">Belum ada peluang yang cocok</h2>
                        <p class="kp-mut mb-3">Coba ganti tipe atau ubah kata kunci.</p>
                        <a class="btn btn-kp-outline" href="{{ route('public.peluang.index') }}">Reset filter</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $opportunities->links('pagination.public') }}
        </div>
    </section>
</div>
@endsection
