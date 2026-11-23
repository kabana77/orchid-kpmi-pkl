@props(['business', 'owner' => null])

@php
    $detailUrl = Route::has('public.usaha.show') ? route('public.usaha.show', $business) : null;
@endphp

<article class="kp-card h-100 d-flex flex-column gap-2">
    <div class="d-flex align-items-center gap-3">
        <div class="kp-lg" aria-hidden="true">{{ $business->inisial }}</div>
        <div>
            <h3 class="h6 mb-1">{{ $business->nama_usaha }}</h3>
            @if ($business->kategori)
                <span class="kp-tag kp-tag-s">{{ $business->kategori }}</span>
            @endif
        </div>
    </div>

    @if ($business->deskripsi)
        <p class="kp-mut small mb-0">{{ Str::limit($business->deskripsi, 140) }}</p>
    @endif

    @if ($owner)
        <div class="kp-mut small">{{ $owner }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
        <span class="kp-bd {{ $business->is_aktif ? 'kp-bd-a' : 'kp-bd-n' }}">{{ $business->is_aktif ? 'Aktif' : 'Non Aktif' }}</span>
        @if ($detailUrl)
            <a class="btn btn-kp-outline btn-sm" href="{{ $detailUrl }}">Lihat usaha</a>
        @endif
    </div>
</article>
