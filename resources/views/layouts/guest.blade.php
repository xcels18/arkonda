<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ARKONDA' }} - Inspektorat Puncak Jaya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-100">
    @php
        $theme = $holidayTheme ?? null;
        $panelGradient = $theme
            ? "background: linear-gradient(135deg, {$theme['colors']['gradient_from']}, {$theme['colors']['gradient_via']}, {$theme['colors']['gradient_to']});"
            : '';
    @endphp
    <div class="min-h-screen flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 {{ $theme ? '' : 'bg-gradient-to-br from-slate-800 via-slate-700 to-slate-900' }} relative overflow-hidden"
            @if($theme) style="{{ $panelGradient }}" @endif>
            <!-- Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-20">
                @if($theme)
                <!-- Holiday Theme Badge -->
                <div class="inline-flex items-center self-start mb-6 px-4 py-1.5 rounded-full bg-white/15 border border-white/25 backdrop-blur-sm">
                    <svg class="w-4 h-4 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                    <span class="text-white text-sm font-medium">{{ $theme['badge_text'] }}</span>
                </div>
                @endif
                <!-- Logo & Title -->
                <div class="mb-8">
                    <!-- Logo Kabupaten Puncak Jaya -->
                    <div class="mb-6">
                        <img src="{{ asset('images/logo-puncak-jaya.png') }}" alt="Logo Kabupaten Puncak Jaya" class="h-24 w-auto drop-shadow-lg">
                    </div>
                    <h1 class="text-4xl xl:text-5xl font-bold text-white mb-3">ARKONDA</h1>
                    <p class="text-xl text-slate-300 font-light">Arsip Kontrak Daerah</p>
                </div>

                <!-- Description -->
                <div class="mb-10">
                    <p class="text-slate-400 text-base leading-relaxed max-w-md">
                        Platform digital untuk pengelolaan dan pengarsipan dokumen kontrak secara aman, terstruktur, dan mudah diakses.
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-slate-300 text-sm">Arsip digital yang aman & terenkripsi</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <span class="text-slate-300 text-sm">Pencarian cepat & filter lengkap</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-slate-300 text-sm">Laporan & export data otomatis</span>
                    </div>
                </div>

                <!-- Organization Badge -->
                <div class="mt-12 pt-8 border-t border-white/10">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">Inspektorat Daerah</p>
                            <p class="text-slate-400 text-xs">Kabupaten Puncak Jaya</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('images/logo-puncak-jaya.png') }}" alt="Logo Kabupaten Puncak Jaya" class="h-16 w-auto mx-auto mb-4">
                    <h1 class="text-2xl font-bold text-slate-800">ARKONDA</h1>
                    <p class="text-slate-500 text-sm">Arsip Kontrak Daerah</p>
                    <p class="text-slate-400 text-xs mt-1">Inspektorat Kabupaten Puncak Jaya</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="text-center mt-8">
                    <p class="text-xs text-slate-400">
                        &copy; {{ date('Y') }} Inspektorat Daerah Kabupaten Puncak Jaya
                    </p>
                    <p class="text-xs text-slate-400 mt-1">
                        ARKONDA - Arsip Kontrak Daerah v1.0
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

