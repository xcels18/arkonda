<x-app-layout>
    <x-slot name="title">Detail Kontrak</x-slot>

    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="{{ route('contracts.index') }}" class="text-slate-500 hover:text-slate-700">Kontrak</a></li>
            <li><svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
            <li class="text-slate-800 font-medium">Detail</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (Left) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Hero Card -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 text-white">
                    <div class="flex items-start justify-between mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $contract->jenis_kontrak == 'Kontrak' ? 'bg-white/20' : 'bg-purple-400/30' }}">
                            {{ $contract->jenis_kontrak ?? 'Lainnya' }}
                        </span>
                        <span class="text-blue-100 text-sm font-medium">{{ $contract->tahun_anggaran }}</span>
                    </div>
                    <h1 class="text-xl font-bold mb-2 leading-tight">{{ $contract->nama_kontrak }}</h1>
                    <p class="text-blue-100 text-sm mb-4">{{ $contract->nomor_kontrak }}</p>
                    <div class="pt-4 border-t border-white/20">
                        <p class="text-blue-100 text-xs uppercase tracking-wider mb-1">Nilai Kontrak</p>
                        <p class="text-2xl font-bold">{{ $contract->getNilaiKontrakFormatted() }}</p>
                    </div>
                </div>
            </div>

            <!-- Contract Info -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-semibold text-slate-800 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Informasi Kontrak
                    </h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">OPD</dt>
                            <dd class="text-sm font-semibold text-slate-800">{{ $contract->opd?->nama ?? '-' }}</dd>
                            @if($contract->opd?->singkatan)
                            <dd class="text-xs text-slate-500">({{ $contract->opd->singkatan }})</dd>
                            @endif
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">Bidang</dt>
                            <dd class="text-sm font-semibold text-slate-800">{{ $contract->bidang ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">Sumber Dana</dt>
                            <dd>
                                @if($contract->sumber_dana)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    {{ $contract->sumber_dana }}
                                </span>
                                @else
                                <span class="text-sm text-slate-400">-</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">Jenis</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $contract->jenis_kontrak == 'Kontrak' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $contract->jenis_kontrak ?? '-' }}
                                </span>
                            </dd>
                        </div>
                        @if($contract->lokasi)
                        <div class="md:col-span-2">
                            <dt class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">Lokasi</dt>
                            <dd class="text-sm text-slate-700 flex items-start">
                                <svg class="w-4 h-4 mr-1.5 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $contract->lokasi }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Provider Info -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-semibold text-slate-800 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Informasi Penyedia
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $contract->nama_penyedia }}</p>
                            <p class="text-xs text-slate-500">Penyedia Jasa/Barang</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-semibold text-slate-800 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Periode Kontrak
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                        <div class="space-y-6">
                            <div class="relative flex items-start">
                                <div class="absolute left-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center border-4 border-white shadow-sm">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                </div>
                                <div class="ml-12">
                                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tanggal Kontrak</p>
                                    <p class="text-sm font-semibold text-slate-800">{{ $contract->tanggal_kontrak?->format('d F Y') ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="relative flex items-start">
                                <div class="absolute left-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center border-4 border-white shadow-sm">
                                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                </div>
                                <div class="ml-12">
                                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tanggal Mulai</p>
                                    <p class="text-sm font-semibold text-slate-800">{{ $contract->tanggal_mulai?->format('d F Y') ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="relative flex items-start">
                                <div class="absolute left-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center border-4 border-white shadow-sm">
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                </div>
                                <div class="ml-12">
                                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tanggal Selesai</p>
                                    <p class="text-sm font-semibold text-slate-800">{{ $contract->tanggal_selesai?->format('d F Y') ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (Right) -->
        <div class="space-y-6">
            <!-- Document Card -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-semibold text-slate-800 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Dokumen Kontrak
                    </h2>
                </div>
                <div class="p-5">
                    @if($contract->hasFile())
                    <div class="text-center mb-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-50 rounded-2xl mb-3">
                            <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-800 mb-1 break-all">{{ $contract->file_name }}</p>
                        <p class="text-xs text-slate-500">{{ $contract->getFileSizeFormatted() }}</p>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('contracts.preview', $contract) }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm font-medium transition-colors ui-button btn-secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview Dokumen
                        </a>
                        <a href="{{ route('contracts.download', $contract) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors ui-button btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                    @else
                    <div class="text-center py-6">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-slate-100 rounded-2xl mb-3">
                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-slate-500">Belum ada dokumen</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes Card -->
            @if($contract->keterangan)
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-semibold text-slate-800 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Keterangan
                    </h2>
                </div>
                <div class="p-5">
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $contract->keterangan }}</p>
                </div>
            </div>
            @endif

            <!-- Back Button -->
            <a href="{{ route('contracts.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 text-sm font-medium transition-colors ui-button btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</x-app-layout>

