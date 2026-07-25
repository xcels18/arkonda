@php
    $query = \App\Models\Contract::query();
    
    if (!auth()->user()->isAdmin() && auth()->user()->opd_id) {
        $query->where('opd_id', auth()->user()->opd_id);
    }
    
    $totalKontrak = $query->count();
    $totalDokumen = (clone $query)->whereNotNull('file_path')->count();
    $kontrakTahunIni = (clone $query)->where('tahun_anggaran', date('Y'))->count();
    $totalNilai = (clone $query)->sum('nilai_kontrak');
    $totalOPD = \App\Models\Opd::count();
    $recentContracts = (clone $query)->with('opd')->latest()->take(5)->get();
    $kontrakPerJenis = (clone $query)->selectRaw('jenis_kontrak, count(*) as total')->groupBy('jenis_kontrak')->pluck('total', 'jenis_kontrak');
@endphp

<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Welcome Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Selamat Datang, {{ auth()->user()->name }}!</h1>
                <p class="text-slate-500 mt-1">Berikut ringkasan data arsip kontrak Anda</p>
            </div>
            <div class="hidden md:flex items-center space-x-2 text-sm text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Total Kontrak -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Kontrak</p>
                        <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalKontrak) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs">
                    <span class="text-slate-500">Kontrak: {{ $kontrakPerJenis['Kontrak'] ?? 0 }}</span>
                    <span class="mx-2 text-slate-300">|</span>
                    <span class="text-slate-500">SPK: {{ $kontrakPerJenis['SPK'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Total Nilai -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Nilai</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ 'Rp ' . number_format($totalNilai / 1000000000, 1, ',', '.') }} M</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-xs text-slate-500">{{ number_format($totalNilai, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Dokumen Terupload -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Dokumen</p>
                        <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalDokumen) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    @if($totalKontrak > 0)
                    <div class="flex items-center">
                        <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                            <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ ($totalDokumen / $totalKontrak) * 100 }}%"></div>
                        </div>
                        <span class="ml-2 text-xs text-slate-500">{{ round(($totalDokumen / $totalKontrak) * 100) }}%</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tahun Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tahun {{ date('Y') }}</p>
                        <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($kontrakTahunIni) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-xs text-slate-500">{{ $totalOPD }} OPD terdaftar</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Contracts -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-800">Kontrak Terbaru</h2>
                <a href="{{ route('contracts.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentContracts as $contract)
                <a href="{{ route('contracts.show', $contract) }}" class="block px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $contract->jenis_kontrak == 'Kontrak' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $contract->jenis_kontrak ?? '-' }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $contract->tahun_anggaran }}</span>
                            </div>
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $contract->nama_kontrak }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $contract->opd?->singkatan ?? $contract->opd?->nama ?? '-' }} • {{ $contract->nama_penyedia }}</p>
                        </div>
                        <div class="ml-4 text-right">
                            <p class="text-sm font-semibold text-emerald-600">{{ $contract->getNilaiKontrakFormatted() }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $contract->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
                @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-slate-500">Belum ada kontrak</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-800">Aksi Cepat</h2>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('contracts.index') }}" class="flex items-center p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors group">
                        <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-slate-800">Cari Kontrak</p>
                            <p class="text-xs text-slate-500">Telusuri arsip kontrak</p>
                        </div>
                    </a>

                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.contracts.create') }}" class="flex items-center p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition-colors group">
                        <div class="w-10 h-10 bg-emerald-100 group-hover:bg-emerald-200 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-slate-800">Tambah Kontrak</p>
                            <p class="text-xs text-slate-500">Input kontrak baru</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="flex items-center p-3 rounded-xl bg-amber-50 hover:bg-amber-100 transition-colors group">
                        <div class="w-10 h-10 bg-amber-100 group-hover:bg-amber-200 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-slate-800">Laporan</p>
                            <p class="text-xs text-slate-500">Export data ke Excel</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.downloads.index') }}" class="flex items-center p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors group">
                        <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-slate-800">Download Arsip</p>
                            <p class="text-xs text-slate-500">Unduh file ZIP</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors group">
                        <div class="w-10 h-10 bg-slate-100 group-hover:bg-slate-200 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-slate-800">Kelola User</p>
                            <p class="text-xs text-slate-500">Manajemen pengguna</p>
                        </div>
                    </a>
                    @elseif(auth()->user()->opd_id)
                    <a href="{{ route('contracts.create') }}" class="flex items-center p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition-colors group">
                        <div class="w-10 h-10 bg-emerald-100 group-hover:bg-emerald-200 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-slate-800">Tambah Kontrak</p>
                            <p class="text-xs text-slate-500">Upload kontrak OPD Anda</p>
                        </div>
                    </a>
                    @endif
                </div>
            </div>

            <!-- User Info Card -->
            <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl shadow-sm overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                            <span class="text-lg font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-300">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    @if(auth()->user()->opd)
                    <div class="mb-3 px-3 py-2 bg-white/10 rounded-lg">
                        <p class="text-xs text-slate-400">OPD</p>
                        <p class="text-sm font-medium text-white">{{ auth()->user()->opd->singkatan ? auth()->user()->opd->singkatan . ' - ' : '' }}{{ auth()->user()->opd->nama }}</p>
                    </div>
                    @endif
                    <div class="flex items-center justify-between pt-3 border-t border-white/10">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ auth()->user()->isAdmin() ? 'bg-blue-500/20 text-blue-200' : 'bg-emerald-500/20 text-emerald-200' }}">
                            {{ auth()->user()->isAdmin() ? 'Administrator' : 'User' }}
                        </span>
                        <span class="text-xs text-slate-400">Login: {{ auth()->user()->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
