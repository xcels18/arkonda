<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    @php
        $isAdmin = auth()->user()->isAdmin();
        $listRoute = $isAdmin ? 'admin.contracts.index' : 'contracts.index';
        $createRoute = $isAdmin ? 'admin.contracts.create' : 'contracts.create';
        $missing = $stats['total'] - $stats['dokumen'];
        $compactValue = $stats['nilai'] >= 1000000000
            ? number_format($stats['nilai'] / 1000000000, 2, ',', '.') . ' miliar'
            : ($stats['nilai'] >= 1000000
                ? number_format($stats['nilai'] / 1000000, 1, ',', '.') . ' juta'
                : number_format($stats['nilai'], 0, ',', '.'));
    @endphp
    <div class="page-header flex justify-between items-center gap-4 mb-6">
        <div><h1>Dashboard arsip</h1><p class="text-slate-500 mt-1">{{ $isAdmin ? 'Ringkasan kontrak seluruh OPD.' : (auth()->user()->opd?->nama ?? 'OPD Anda belum ditetapkan.') }}</p></div>
        @if($isAdmin || auth()->user()->opd_id)<a href="{{ route($createRoute) }}" class="ui-button btn-primary">+ Tambah kontrak</a>@endif
    </div>
    @if(!$isAdmin && !auth()->user()->opd_id)<div class="ui-alert ui-alert-info">Hubungi administrator untuk menetapkan OPD Anda agar arsip kontrak dapat diakses.</div>@endif
    <div class="stats-grid grid grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="ui-card p-5"><p class="text-xs text-slate-500">Total kontrak</p><p class="text-3xl font-semibold mt-2 tabular-nums">{{ number_format($stats['total'], 0, ',', '.') }}</p><p class="text-[11px] text-slate-500 mt-3">Kontrak dan SPK terdaftar</p></div>
        <div class="ui-card p-5"><p class="text-xs text-slate-500">Total nilai kontrak</p><p class="text-xl font-semibold text-blue-600 mt-3 tabular-nums break-words"><span class="hidden sm:inline">Rp {{ number_format($stats['nilai'], 0, ',', '.') }}</span><span class="sm:hidden" aria-label="Rp {{ number_format($stats['nilai'], 0, ',', '.') }}">Rp {{ $compactValue }}</span></p><p class="text-[11px] text-slate-500 mt-3">Seluruh tahun anggaran</p></div>
        <div class="ui-card p-5"><p class="text-xs text-slate-500">Dokumen tersedia</p><p class="text-3xl font-semibold mt-2 tabular-nums">{{ number_format($stats['dokumen'], 0, ',', '.') }} <span class="text-sm font-normal text-slate-500">/ {{ $stats['total'] }}</span></p><p class="text-[11px] text-slate-500 mt-3">{{ $stats['total'] === 0 ? 'Belum ada kontrak terdaftar' : ($missing ? $missing . ' kontrak belum memiliki dokumen' : 'Semua kontrak sudah dilengkapi dokumen') }}</p></div>
        <div class="ui-card p-5"><p class="text-xs text-slate-500">Tahun anggaran {{ now()->year }}</p><p class="text-3xl font-semibold mt-2 tabular-nums">{{ number_format($stats['tahun'], 0, ',', '.') }}</p><p class="text-[11px] text-slate-500 mt-3">Kontrak pada tahun berjalan</p></div>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="ui-card xl:col-span-2 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap gap-3 items-center justify-between"><h2 class="text-base">Kontrak terbaru</h2><a href="{{ route($listRoute) }}" class="text-xs text-blue-600 font-medium">Lihat semua kontrak &rarr;</a></div>
            <div class="divide-y divide-slate-200">
                @forelse($recentContracts as $contract)
                    <a href="{{ route($isAdmin ? 'admin.contracts.show' : 'contracts.show', $contract) }}" class="block px-5 py-4 hover:bg-slate-50">
                        <div class="flex flex-wrap gap-2 items-center mb-1"><span class="text-xs font-medium text-blue-600">{{ $contract->nomor_kontrak }}</span><span class="text-[10px] bg-blue-100 text-blue-800 rounded-full px-2 py-0.5">{{ $contract->jenis_kontrak }} · {{ $contract->tahun_anggaran }}</span></div>
                        <p class="text-sm font-medium break-words">{{ $contract->nama_kontrak }}</p>
                        <div class="flex flex-wrap justify-between gap-2 mt-2 text-xs"><span class="text-slate-500">{{ $contract->opd?->singkatan ?: $contract->opd?->nama }} · {{ $contract->nama_penyedia }}</span><span class="font-medium tabular-nums">{{ $contract->getNilaiKontrakFormatted() }}</span></div>
                        <p class="text-[11px] mt-1 {{ $contract->hasFile() ? 'text-emerald-700' : 'text-yellow-800' }}">{{ $contract->hasFile() ? 'Dokumen tersedia' : 'Belum ada dokumen' }}</p>
                    </a>
                @empty
                    <div class="empty-state"><strong>Belum ada kontrak</strong><p>{{ $isAdmin || auth()->user()->opd_id ? 'Tambahkan kontrak pertama untuk mulai menyusun arsip.' : 'Arsip akan tampil setelah OPD Anda ditetapkan.' }}</p>@if($isAdmin || auth()->user()->opd_id)<a class="ui-button btn-secondary mt-4" href="{{ route($createRoute) }}">Tambah kontrak</a>@endif</div>
                @endforelse
            </div>
        </section>
        <div class="space-y-6">
            <section class="ui-card p-5"><h2 class="text-base mb-4">Perlu perhatian</h2>
                <p class="text-sm font-medium">{{ $missing }} kontrak belum dilengkapi PDF</p><p class="text-xs text-slate-500 mt-1">Lengkapi dokumen agar arsip dapat diperiksa dan diunduh.</p>
                <a href="{{ route($isAdmin ? 'admin.reports.index' : 'contracts.index') }}" class="text-xs text-blue-600 font-medium inline-block mt-3">{{ $isAdmin ? 'Periksa laporan' : 'Periksa daftar kontrak' }} &rarr;</a>
                @if($isAdmin)<div class="pt-4 mt-4 border-t border-slate-200"><p class="text-sm font-medium">{{ $pendingUsers }} pengguna menunggu verifikasi</p><a href="{{ route('admin.users.index', ['status' => 'unverified', 'role' => 'user']) }}" class="text-xs text-blue-600 font-medium inline-block mt-2">Tinjau pengguna &rarr;</a></div>@endif
            </section>
            <section class="ui-card p-5"><h2 class="text-base mb-4">Akses cepat</h2><div class="space-y-2"><a class="ui-button btn-secondary w-full justify-start" href="{{ route($listRoute) }}">Cari kontrak</a>@if($isAdmin)<a class="ui-button btn-secondary w-full justify-start" href="{{ route('admin.reports.index') }}">Rekap dan laporan</a><a class="ui-button btn-secondary w-full justify-start" href="{{ route('admin.downloads.index') }}">Unduh dokumen ZIP</a>@endif</div></section>
        </div>
    </div>
</x-app-layout>
