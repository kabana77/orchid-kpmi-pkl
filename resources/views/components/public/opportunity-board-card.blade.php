@props(['opportunity'])

@php
    $pemasang = $opportunity->pemasang;
    $url = route('public.peluang.show', $opportunity);
@endphp

<article class="kp-card h-100 d-flex flex-column gap-2">
    <div class="d-flex justify-content-between align-items-center gap-2">
        <span class="kp-chip">{{ $opportunity->tipe_label }}</span>
        @if ($opportunity->batas_label)
            <span class="kp-mut small">Berlaku hingga {{ $opportunity->batas_label }}</span>
        @endif
    </div>

    <h3 class="h5 mb-0">
        <a class="text-decoration-none" style="color:var(--kp-navy)" href="{{ $url }}">{{ $opportunity->judul }}</a>
    </h3>

    @if ($opportunity->deskripsi)
        <p class="kp-mut mb-0" style="line-height:1.55">{{ Str::limit(strip_tags((string) $opportunity->deskripsi), 140) }}</p>
    @endif

    @if ($opportunity->lokasi)
        <div class="kp-mut small">{{ $opportunity->lokasi }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-auto pt-2">
        @if ($pemasang)
            <div class="d-flex align-items-center gap-2">
                <div class="kp-av" style="width:34px;height:34px;font-size:.8rem" aria-hidden="true">{{ $pemasang->inisial }}</div>
                <span class="small fw-medium">{{ $pemasang->nama_lengkap }}</span>
            </div>
        @endif
        <a class="btn btn-kp-outline btn-sm" href="{{ $url }}">Lihat detail</a>
    </div>
</article>
