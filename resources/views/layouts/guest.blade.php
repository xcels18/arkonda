<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ARKONDA' }} — Inspektorat Puncak Jaya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="guest-shell">
        <header class="guest-header">
            <div class="guest-identity">
                @if(file_exists(public_path('images/logo-inspektorat.png')))
                    <img src="{{ asset('images/logo-inspektorat.png') }}" alt="Logo Inspektorat Kabupaten Puncak Jaya">
                @endif
                <span class="brand-mark" aria-hidden="true">a</span>
                <div><strong>ARKONDA</strong><small>INSPEKTORAT PUNCAK JAYA</small></div>
                <a class="guest-header-link" href="{{ route('login') }}">Ruang Petugas <span aria-hidden="true">↗</span></a>
            </div>
        </header>
        <main class="guest-main">
            <div class="guest-card ui-card">
                {{ $slot }}
            </div>
        </main>
        <footer class="guest-footer">&copy; {{ date('Y') }} Inspektorat Kabupaten Puncak Jaya</footer>
    </div>
</body>
</html>
