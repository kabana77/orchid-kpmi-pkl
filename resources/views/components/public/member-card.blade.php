@props(['member', 'showSkills' => false])

@php
    $detailUrl = Route::has('public.anggota.show') ? route('public.anggota.show', $member) : null;
    $skills = $member->skills;
@endphp

<article class="kp-card h-100 d-flex flex-column gap-2">
    @if ($member->foto_url)
        <img class="kp-av" src="{{ $member->foto_url }}" alt="" width="56" height="56" loading="lazy">
    @else
        <div class="kp-av" aria-hidden="true">{{ $member->inisial }}</div>
    @endif

    <h3 class="h5 mb-0">{{ $member->nama_lengkap }}</h3>

    @if ($member->kota)
        <div class="kp-mut small">{{ $member->kota }}</div>
    @endif

    @if ($member->professionalRoles->isNotEmpty())
        <div>
            @foreach ($member->professionalRoles as $role)
                <span class="kp-tag me-1 mb-1">{{ $role->nama }}</span>
            @endforeach
        </div>
    @endif

    @if ($showSkills && $skills->isNotEmpty())
        <div>
            @foreach ($skills->take(3) as $skill)
                <span class="kp-tag kp-tag-s me-1 mb-1">{{ $skill->nama }}</span>
            @endforeach
            @if ($skills->count() > 3)
                <span class="kp-mut small">+{{ $skills->count() - 3 }} lainnya</span>
            @endif
        </div>
    @endif

    @if ($detailUrl)
        <div class="mt-auto pt-2">
            <a class="btn btn-kp-outline" href="{{ $detailUrl }}">Lihat profil</a>
        </div>
    @endif
</article>
