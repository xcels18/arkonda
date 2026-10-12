<x-app-layout>
    <x-slot name="title">Detail Kontrak</x-slot>

    <!-- Header Section -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm">
                <li><a href="{{ route('admin.contracts.index') }}" class="text-slate-500 hover:text-blue-600 transition-colors">Kelola Kontrak</a></li>
                <li class="text-slate-400">/</li>
                <li class="text-slate-700 font-medium">Detail</li>
            </ol>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Detail Kontrak</h1>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $contract->jenis_kontrak == 'Kontrak' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ $contract->jenis_kontrak ?? 'N/A' }}
                    </span>
                </div>
                <p class="text-base text-slate-500">{{ $contract->nomor_kontrak }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.contracts.edit', $contract) }}" class="inline-flex items-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.contracts.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-medium rounded-lg border border-slate-300 transition-all">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Nama Kontrak Card -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
                <p class="text-blue-200 text-sm font-medium mb-1">Nama Kontrak</p>
                <h2 class="text-2xl font-bold leading-tight">{{ $contract->nama_kontrak }}</h2>
                <div class="mt-4 flex items-center gap-6">
                    <div>
                        <p class="text-blue-200 text-xs uppercase tracking-wide">Tahun Anggaran</p>
                        <p class="text-xl font-bold">{{ $contract->tahun_anggaran }}</p>
                    </div>
                    <div class="h-10 w-px bg-blue-400/50"></div>
                    <div>
                        <p class="text-blue-200 text-xs uppercase tracking-wide">Nilai Kontrak</p>
                        <p class="text-xl font-bold">{{ $contract->getNilaiKontrakFormatted() }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Kontrak -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">Informasi Kontrak</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Data lengkap kontrak</p>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Nomor Kontrak</dt>
                            <dd class="text-base font-semibold text-slate-800">{{ $contract->nomor_kontrak }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Jenis Kontrak</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-1 text-sm font-medium rounded-md {{ $contract->jenis_kontrak == 'Kontrak' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                    {{ $contract->jenis_kontrak ?? '-' }}
                                </span>
                            </dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">OPD</dt>
                            <dd class="text-base text-slate-800">{{ $contract->opd?->getDisplayName() ?? '-' }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bidang</dt>
                            <dd class="text-base text-slate-800">{{ $contract->bidang ?? '-' }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Sumber Dana</dt>
                            <dd>
                                @if($contract->sumber_dana)
                                <span class="inline-flex items-center px-2.5 py-1 text-sm font-medium rounded-md bg-emerald-50 text-emerald-700">
                                    {{ $contract->sumber_dana }}
                                </span>
                                @else
                                <span class="text-slate-400">-</span>
                                @endif
                            </dd>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Lokasi</dt>
                            <dd class="text-base text-slate-800">{{ $contract->lokasi ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Informasi Penyedia -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">Informasi Penyedia</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Data rekanan/vendor</p>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-800">{{ $contract->nama_penyedia }}</h3>
                            <p class="text-sm text-slate-500 mt-1">Penyedia Barang/Jasa</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Nilai Kontrak</p>
                            <p class="text-lg font-bold text-emerald-600">{{ $contract->getNilaiKontrakFormatted() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Periode Kontrak -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">Periode Kontrak</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Timeline pelaksanaan</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-1 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                <div class="w-2.5 h-2.5 bg-blue-600 rounded-full"></div>
                            </div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tanggal Kontrak</p>
                            <p class="text-base font-semibold text-slate-800 mt-1">{{ $contract->tanggal_kontrak?->format('d F Y') ?? '-' }}</p>
                        </div>
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-1 w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center">
                                <div class="w-2.5 h-2.5 bg-emerald-600 rounded-full"></div>
                            </div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tanggal Mulai</p>
                            <p class="text-base font-semibold text-slate-800 mt-1">{{ $contract->tanggal_mulai?->format('d F Y') ?? '-' }}</p>
                        </div>
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-1 w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                <div class="w-2.5 h-2.5 bg-red-600 rounded-full"></div>
                            </div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tanggal Selesai</p>
                            <p class="text-base font-semibold text-slate-800 mt-1">{{ $contract->tanggal_selesai?->format('d F Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Document & Meta -->
        <div class="space-y-6">
            <!-- Dokumen Kontrak -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-white border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">Dokumen Kontrak</h2>
                    <p class="text-sm text-slate-500 mt-0.5">File scan kontrak</p>
                </div>
                <div class="p-6">
                    @if($contract->hasFile())
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ $contract->file_name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $contract->getFileSizeFormatted() }}</p>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Diupload {{ $contract->uploaded_at?->diffForHumans() ?? '-' }}
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('contracts.preview', $contract) }}" target="_blank"
                               class="flex items-center justify-center px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-medium rounded-lg border border-slate-300 transition-all text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Preview
                            </a>
                            <a href="{{ route('contracts.download', $contract) }}"
                               class="flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all text-sm ui-button btn-primary">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-slate-500">Belum ada dokumen</p>
                        <a href="{{ route('admin.contracts.edit', $contract) }}" class="inline-flex items-center mt-3 text-sm text-blue-600 hover:text-blue-700 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Upload Dokumen
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Keterangan -->
            <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-800">Keterangan</h2>
                </div>
                <div class="p-6">
                    @if($contract->keterangan)
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $contract->keterangan }}</p>
                    @else
                    <p class="text-sm text-slate-400 italic">Tidak ada keterangan</p>
                    @endif
                </div>
            </div>

            <!-- Meta Info -->
            <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Informasi Sistem</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">ID Kontrak</span>
                        <span class="font-mono text-slate-700 bg-white px-2 py-0.5 rounded">#{{ $contract->id }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Diupload oleh</span>
                        <span class="text-slate-700 font-medium">{{ $contract->uploader?->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Dibuat</span>
                        <span class="text-slate-700">{{ $contract->created_at?->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Diperbarui</span>
                        <span class="text-slate-700">{{ $contract->updated_at?->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl  border border-slate-200 p-6 ui-card">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.contracts.edit', $contract) }}" class="flex items-center w-full px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Kontrak
                    </a>
                    <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kontrak ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Kontrak
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

