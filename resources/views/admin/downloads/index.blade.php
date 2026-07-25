<x-app-layout>
    <x-slot name="title">Download Arsip</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Download Arsip Kontrak</h1>
        <p class="text-slate-500">Download file kontrak dalam bentuk ZIP dengan struktur folder bertingkat</p>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">Filter Dokumen</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="tahun" class="px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="">Semua Tahun</option>
                @foreach($years as $year)
                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <select name="opd_id" class="px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="">Semua OPD</option>
                @foreach($opds as $opd)
                <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>{{ $opd->singkatan ?? $opd->nama }}</option>
                @endforeach
            </select>
            <select name="jenis" class="px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="">Semua Jenis</option>
                @foreach(\App\Models\Contract::JENIS_OPTIONS as $value => $label)
                <option value="{{ $value }}" {{ request('jenis') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="sumber_dana" class="px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="">Semua Sumber Dana</option>
                @foreach(\App\Models\Contract::SUMBER_DANA_OPTIONS as $value => $label)
                <option value="{{ $value }}" {{ request('sumber_dana') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('admin.downloads.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm font-medium transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow-sm rounded-xl border border-slate-200 p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-500">Total File</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalFiles }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-sm rounded-xl border border-slate-200 p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-100">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-500">Total Ukuran</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalSize / 1024 / 1024, 2) }} MB</p>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-sm rounded-xl border border-slate-200 p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-500">Total Folder</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $grouped->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Button -->
    @if($totalFiles > 0)
    <div class="mb-6 flex items-center justify-between">
        <label class="inline-flex items-center text-sm text-slate-600">
            <input type="checkbox" id="select-all" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mr-2" checked>
            Pilih Semua
        </label>
        <button type="submit" form="download-form" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-sm font-semibold transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download ZIP (<span id="selected-count">{{ $totalFiles }}</span> file)
        </button>
    </div>
    @endif

    <!-- Folder Preview -->
    <form method="POST" action="{{ route('admin.downloads.zip') }}" id="download-form">
        @csrf
        <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-sm font-semibold text-slate-700">Preview Struktur Folder</h2>
            </div>
            <div class="p-5">
                @forelse($grouped as $opdName => $jenisGroups)
                <div class="mb-4 last:mb-0">
                    <div class="flex items-center text-slate-700 font-medium mb-2">
                        <svg class="w-5 h-5 mr-2 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                        </svg>
                        {{ $opdName }}/
                    </div>
                    @foreach($jenisGroups as $jenisName => $contracts)
                    <div class="ml-6 mb-2">
                        <div class="flex items-center text-slate-600 mb-1">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                            </svg>
                            {{ $jenisName }}/ <span class="ml-2 text-xs text-slate-400">({{ $contracts->count() }} file)</span>
                        </div>
                        <div class="ml-6 space-y-1">
                            @foreach($contracts as $contract)
                            <label class="flex items-center text-xs text-slate-500 hover:bg-slate-50 rounded px-1 py-0.5 cursor-pointer">
                                <input type="checkbox" name="contract_ids[]" value="{{ $contract->id }}" class="contract-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mr-2" checked>
                                <svg class="w-3 h-3 mr-2 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                {{ $contract->nama_kontrak }}_{{ $contract->nama_penyedia }}_{{ $contract->tahun_anggaran }}.pdf
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-slate-500">Tidak ada file kontrak yang tersedia untuk didownload.</p>
                </div>
                @endforelse
            </div>
        </div>
    </form>

    @if($totalFiles > 0)
    <script>
        (function () {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.contract-checkbox');
            const selectedCount = document.getElementById('selected-count');

            function updateCount() {
                const checked = document.querySelectorAll('.contract-checkbox:checked').length;
                selectedCount.textContent = checked;
                selectAll.checked = checked === checkboxes.length;
            }

            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateCount();
            });

            checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
        })();
    </script>
    @endif
</x-app-layout>

