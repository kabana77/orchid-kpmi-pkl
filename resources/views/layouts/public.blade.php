<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KPMI Pekalongan')</title>
    <meta name="description" content="@yield('description', 'Direktori anggota dan usaha komunitas KPMI Pekalongan.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">
    <a class="visually-hidden-focusable p-2" href="#isi">Lewati ke konten</a>

    <x-public.navbar />

    <main id="isi" class="flex-grow-1">
        @yield('content')
    </main>

    <x-public.footer />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
