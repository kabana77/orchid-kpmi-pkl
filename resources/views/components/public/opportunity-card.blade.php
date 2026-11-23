@props(['opportunity'])

@php
    $meta = array_filter([
        $opportunity->lokasi,
        $opportunity->batas_label ? 'Berlaku hingga '.$opportunity->batas_label : null,
    ]);
@endphp

<article class="kp-card d-flex gap-3 align-items-start">
    <span class="kp-chip mt-1">{{ $opportunity->tipe_label }}</span>
    <div>
        <h3 class="h6 mb-1">
            <a class="text-decoration-none" style="color:var(--kp-navy)" href="{{ route('public.peluang.show', $opportunity) }}">{{ $opportunity->judul }}</a>
        </h3>
        <div class="kp-mut small">{{ implode(' · ', $meta) }}</div>
    </div>
</article>
