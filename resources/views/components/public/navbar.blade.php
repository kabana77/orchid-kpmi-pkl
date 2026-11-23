<nav class="navbar navbar-expand-md kp-nav">
    <div class="container">
        <a class="navbar-brand kp-brand d-flex align-items-center" href="{{ route('public.home') }}">
            <img src="{{ asset('images/logo-kpmi.png') }}" alt="Logo KPMI" style="height: 40px; width: auto; margin-right: 10px;">
            <span>KPMI Pekalongan</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#kpNav"
                aria-controls="kpNav" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="kpNav">
            <ul class="navbar-nav me-auto gap-md-2 ms-md-4">
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('public.home')) active @endif" href="{{ route('public.home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (request()->routeIs('public.anggota.*')) active @endif" href="{{ route('public.anggota.index') }}"
                       @if (request()->routeIs('public.anggota.*')) aria-current="page" @endif>Anggota</a>
                </li>
                @if (Route::has('public.usaha.index'))
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('public.usaha.*')) active @endif" href="{{ route('public.usaha.index') }}">Usaha</a>
                    </li>
                @endif
                @if (Route::has('public.peluang.index'))
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('public.peluang.*')) active @endif" href="{{ route('public.peluang.index') }}">Peluang</a>
                    </li>
                @endif
                @if (Route::has('public.event.index'))
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('public.event.*')) active @endif" href="{{ route('public.event.index') }}">Event</a>
                    </li>
                @endif
            </ul>

            @auth
                <a class="btn btn-kp" href="{{ route('platform.main') }}">Area anggota</a>
            @else
                <a class="btn btn-kp" href="{{ route('platform.login') }}">Masuk</a>
            @endauth
        </div>
    </div>
</nav>
