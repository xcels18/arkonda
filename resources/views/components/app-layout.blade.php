<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — ARKONDA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" x-data="{ mobileOpen: false }">
    <a class="skip-link" href="#main-content">Lewati navigasi, menuju konten</a>
    <aside class="app-sidebar" aria-label="Navigasi aplikasi">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3" aria-label="ARKONDA, buka dashboard">
                <span class="brand-mark" aria-hidden="true">a</span>
                <span><strong>ARKONDA</strong><small>INSPEKTORAT PUNCAK JAYA</small></span>
            </a>
            <button type="button" class="mobile-menu-button" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-controls="sidebar-navigation" aria-label="Buka atau tutup navigasi">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <nav id="sidebar-navigation" class="sidebar-nav" data-open="false" :data-open="mobileOpen.toString()" @keydown.escape="mobileOpen = false">
            <p class="nav-section">RUANG KERJA</p>
            <x-nav-link route="dashboard" pattern="dashboard" icon="dashboard">Dashboard</x-nav-link>
            @if(auth()->user()->isAdmin())
                <x-nav-link route="admin.contracts.index" pattern="admin.contracts.*" icon="document">Kelola kontrak</x-nav-link>
                <x-nav-link route="admin.reports.index" pattern="admin.reports.*" icon="report">Laporan</x-nav-link>
                <x-nav-link route="admin.downloads.index" pattern="admin.downloads.*" icon="download">Unduh arsip</x-nav-link>
                <p class="nav-section mt-7">MASTER DATA</p>
                <x-nav-link route="admin.opds.index" pattern="admin.opds.*" icon="building">Organisasi / OPD</x-nav-link>
                <x-nav-link route="admin.users.index" pattern="admin.users.*" icon="users">Pengguna</x-nav-link>
            @else
                <x-nav-link route="contracts.index" pattern="contracts.*" icon="document">Kontrak OPD</x-nav-link>
            @endif
        </nav>
        <div class="sidebar-footer"><p class="font-medium text-white">Inspektorat</p><p>Kabupaten Puncak Jaya</p></div>
    </aside>
    <div class="app-workspace">
        <header class="app-topbar">
            <div class="topbar-context"><strong>Inspektorat Kabupaten Puncak Jaya</strong><span class="hidden sm:inline">{{ $title ?? 'Dashboard' }}</span></div>
            <div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
                <button type="button" class="account-trigger" @click="open = !open" :aria-expanded="open" aria-controls="account-menu" aria-label="Menu akun {{ auth()->user()->name }}">
                    <span class="account-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
                    <span class="hidden sm:block"><span class="block text-xs font-semibold">{{ auth()->user()->name }}</span><span class="block text-[10px] text-slate-500">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Petugas OPD' }}</span></span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div id="account-menu" class="account-menu" x-show="open" x-cloak @click.outside="open = false">
                    <p class="text-sm font-semibold">{{ auth()->user()->name }}</p><p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    <p class="text-xs text-slate-500 mt-2">{{ auth()->user()->opd?->nama ?? 'Belum ditetapkan ke OPD' }}</p>
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">@csrf<button type="submit" class="ui-button btn-secondary w-full">Keluar dari akun</button></form>
                </div>
            </div>
        </header>
        <main id="main-content" class="app-content" tabindex="-1">
            @foreach(['success' => 'success', 'error' => 'error', 'info' => 'info'] as $key => $kind)
                @if(session($key))<div class="ui-alert ui-alert-{{ $kind }}" role="{{ $key === 'error' ? 'alert' : 'status' }}">{{ session($key) }}</div>@endif
            @endforeach
            @if($errors->any())
                <div class="ui-alert ui-alert-error" role="alert"><strong>Data belum dapat disimpan.</strong><p>Periksa dan perbaiki kolom berikut.</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            {{ $slot }}
        </main>
    </div>
</body>
</html>
