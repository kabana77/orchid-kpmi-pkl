@extends('layouts.public')

@section('title', 'Direktori Usaha · KPMI Pekalongan')

@section('content')
@php $status = $filters['status'] ?? 'aktif'; @endphp
<div class="container pt-4 pb-5">
    <div class="kp-mut small">Beranda / Usaha</div>

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mt-2 mb-4">
        <div>
            <h1 class="h2 mb-1">Direktori Usaha</h1>
            <p class="kp-mut mb-0">Usaha milik anggota komunitas, siap untuk kerja sama dan referral.</p>
        </div>
        <span class="kp-mut small">
            @if ($businesses->total())
                Menampilkan {{ $businesses->firstItem() }} sampai {{ $businesses->lastItem() }} dari {{ $businesses->total() }} usaha
            @else
                Tidak ada usaha ditemukan
            @endif
        </span>
    </div>

    <div class="row g-4 align-items-start">
        <aside class="col-lg-3">
            <form method="GET" action="{{ route('public.usaha.index') }}" class="kp-card d-flex flex-column gap-3">
                <h2 class="h5 mb-0">Filter</h2>

                <div>
                    <label class="form-label" for="q">Nama usaha</label>
                    <input id="q" name="q" type="search" class="form-control" maxlength="100"
                           value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama usaha">
                </div>

                <div>
                    <label class="form-label" for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" class="form-select">
                        <option value="">Semua kategori</option>
                        @foreach ($options['kategori'] as $kategori)
                            <option value="{{ $kategori }}" @selected(($filters['kategori'] ?? null) === $kategori)>{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected($status === 'nonaktif')>Non Aktif</option>
                        <option value="semua" @selected($status === 'semua')>Semua</option>
                    </select>
                </div>

                <p class="kp-mut small mb-0">Secara default hanya usaha aktif yang tampil. Riwayat usaha tetap tersimpan di profil pemiliknya.</p>

                <button type="submit" class="btn btn-kp">Terapkan filter</button>
                <a class="btn btn-kp-outline" href="{{ route('public.usaha.index') }}">Reset</a>
            </form>
        </aside>

        <section class="col-lg-9" aria-label="Daftar usaha">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                @forelse ($businesses as $business)
                    <div class="col">
                        <x-public.business-card
                            :business="$business"
                            :owner="$business->member->nama_lengkap.($business->member->kota ? ' · '.$business->member->kota : '')" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="kp-card text-center py-5">
                            <h2 class="h5">Belum ada usaha yang cocok</h2>
                            <p class="kp-mut mb-3">Coba kurangi filter atau ubah kata kunci.</p>
                            <a class="btn btn-kp-outline" href="{{ route('public.usaha.index') }}">Reset filter</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $businesses->links('pagination.public') }}
            </div>
        </section>
    </div>
</div>
@endsection
