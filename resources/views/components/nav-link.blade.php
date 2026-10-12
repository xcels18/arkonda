@props(['route', 'pattern', 'icon'])
@php
    $paths = [
        'dashboard' => 'M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z',
        'document' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8M8 17h6',
        'report' => 'M4 3v18h17M8 17v-5M13 17V7M18 17v-8',
        'download' => 'M12 3v12m-5-5 5 5 5-5M4 16v4h16v-4',
        'building' => 'M5 21V3h14v18M3 21h18M9 7h1m4 0h1M9 11h1m4 0h1M10 21v-6h4v6',
        'users' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M16 3a4 4 0 0 1 0 8m6 10v-2a4 4 0 0 0-3-3.87M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0',
    ];
@endphp
<a href="{{ route($route) }}" class="nav-link" @if(request()->routeIs($pattern)) aria-current="page" @endif>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $paths[$icon] }}"/></svg>
    <span>{{ $slot }}</span>
</a>
