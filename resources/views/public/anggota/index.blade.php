@extends('layouts.public')

@section('title', 'Direktori Anggota · KPMI Pekalongan')

@section('content')
<div class="container pt-4 pb-5">
    <div class="kp-mut small">Beranda / Anggota</div>

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mt-2 mb-4">
        <div>
            <h1 class="h2 mb-1">Direktori Anggota</h1>
            <p class="kp-mut mb-0">Temukan rekan usaha, mitra, dan profesional di komunitas.</p>
        </div>
        <span class="kp-mut small">
            @if ($members->total())
                Menampilkan {{ $members->firstItem() }} sampai {{ $members->lastItem() }} dari {{ $members->total() }} anggota
            @else
                Tidak ada anggota ditemukan
            @endif
        </span>
    </div>

    <div class="row g-4 align-items-start">
        <aside class="col-lg-3">
            <form method="GET" action="{{ route('public.anggota.index') }}" class="kp-card d-flex flex-column gap-3">
                <h2 class="h5 mb-0">Filter</h2>

                <div>
                    <label class="form-label" for="q">Nama</label>
                    <input id="q" name="q" type="search" class="form-control" maxlength="100"
                           value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama anggota">
                </div>

                <div>
                    <label class="form-label" for="kota">Kota</label>
                    <select id="kota" name="kota" class="form-select">
                        <option value="">Semua kota</option>
                        @foreach ($options['kota'] as $kota)
                            <option value="{{ $kota }}" @selected(($filters['kota'] ?? null) === $kota)>{{ $kota }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" for="skill">Skill</label>
                    <select id="skill" name="skill" class="form-select">
                        <option value="">Semua skill</option>
                        @foreach ($options['skills'] as $skill)
                            <option value="{{ $skill->id }}" @selected(($filters['skill'] ?? null) == $skill->id)>{{ $skill->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <fieldset>
                    <legend class="form-label fs-6">Peran</legend>
                    @foreach ($options['roles'] as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="peran[]" id="peran-{{ $role->id }}"
                                   value="{{ $role->id }}" @checked(in_array($role->id, $filters['peran'] ?? []))>
                            <label class="form-check-label" for="peran-{{ $role->id }}">{{ $role->nama }}</label>
                        </div>
                    @endforeach
                </fieldset>

                <button type="submit" class="btn btn-kp">Terapkan filter</button>
                <a class="btn btn-kp-outline" href="{{ route('public.anggota.index') }}">Reset</a>
            </form>
        </aside>

        <section class="col-lg-9" aria-label="Daftar anggota">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                @forelse ($members as $member)
                    <div class="col">
                        <x-public.member-card :member="$member" :show-skills="true" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="kp-card text-center py-5">
                            <h2 class="h5">Belum ada anggota yang cocok</h2>
                            <p class="kp-mut mb-3">Coba kurangi filter atau ubah kata kunci nama.</p>
                            <a class="btn btn-kp-outline" href="{{ route('public.anggota.index') }}">Reset filter</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $members->links('pagination.public') }}
            </div>
        </section>
    </div>
</div>
@endsection
