<x-app-layout>
    <x-slot name="title">Kontrak {{ $userOpd ? $userOpd->singkatan ?? $userOpd->nama : '' }}</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daftar Kontrak</h1>
            @if($userOpd)
            <p class="text-slate-500 mt-1">
                <span class="inline-flex items-center px-2.5 py-1 text-sm font-medium rounded-lg bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ $userOpd->singkatan ? $userOpd->singkatan . ' - ' : '' }}{{ $userOpd->nama }}
                </span>
            </p>
            @else
            <p class="text-slate-500">Cari dan lihat dokumen kontrak yang tersedia</p>
            @endif
        </div>
        @if($userOpd)
        <a href="{{ route('contracts.create') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg  transition-colors ui-button btn-secondary table-action-button">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kontrak
        </a>
        @endif
    </div>

    <!-- Search & Filters -->
    <div class="bg-white  rounded-xl border border-slate-200 p-5 mb-6 ui-card">
        <form method="GET" class="ui-filter space-y-4">
            <!-- Row 1: Search -->
            <div>
                <div class="min-w-0"><label for="filter-search">Pencarian</label><input type="text" id="filter-search" name="search" value="{{ request('search') }}" placeholder="Cari nomor/nama kontrak/penyedia..."
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm ui-input"></div>
            </div>
            <!-- Row 2: Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="min-w-0"><label for="filter-tahun">Tahun anggaran</label><select id="filter-tahun" name="tahun" class="px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm ui-input">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select></div>
                <div class="min-w-0"><label for="filter-jenis">Jenis kontrak</label><select id="filter-jenis" name="jenis" class="px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm ui-input">
                    <option value="">Semua Jenis</option>
                    @foreach(\App\Models\Contract::JENIS_OPTIONS as $value => $label)
                    <option value="{{ $value }}" {{ request('jenis') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select></div>
                <div class="min-w-0"><label for="filter-penyedia">Penyedia</label><select id="filter-penyedia" name="penyedia" class="px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm ui-input">
                    <option value="">Semua Penyedia</option>
                    @foreach($penyediaOptions as $penyedia)
                    <option value="{{ $penyedia }}" {{ request('penyedia') == $penyedia ? 'selected' : '' }}>{{ Str::limit($penyedia, 30) }}</option>
                    @endforeach
                </select></div>
            </div>
            <!-- Row 3: Buttons -->
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors ui-button btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
                <a href="{{ route('contracts.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm font-medium transition-colors ui-button btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    @if(request()->hasAny(['search', 'tahun', 'jenis', 'penyedia']))
    <div class="mb-4 text-sm text-slate-600">
        Ditemukan <span class="font-semibold text-slate-800">{{ $contracts->total() }}</span> kontrak
        @if(request('search')) dengan kata kunci "<span class="font-medium">{{ request('search') }}</span>"@endif
    </div>
    @endif

    <!-- Contracts Table -->
    <div class="bg-white  rounded-xl border border-slate-200 overflow-hidden ui-card">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-700">
                    Menampilkan <span class="font-medium">{{ $contracts->firstItem() ?? 0 }}</span>
                    sampai <span class="font-medium">{{ $contracts->lastItem() ?? 0 }}</span>
                    dari <span class="font-medium">{{ $contracts->total() }}</span> kontrak
                </p>
                <div class="text-sm text-slate-500">
                    Halaman {{ $contracts->currentPage() }} dari {{ $contracts->lastPage() }}
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider w-16">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Nomor Kontrak</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider" style="min-width: 250px;">Nama Kontrak</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">OPD</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider" style="min-width: 180px;">Penyedia</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">Nilai Kontrak</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Tahun</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Dokumen</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider" style="min-width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($contracts as $index => $contract)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-slate-500">
                            {{ $contracts->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-900">{{ $contract->nomor_kontrak }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <p class="text-sm text-slate-900 font-medium line-clamp-2" title="{{ $contract->nama_kontrak }}">{{ $contract->nama_kontrak }}</p>
                                @if($contract->jenis_kontrak)
                                <span class="inline-flex items-center mt-1.5 px-2 py-0.5 text-xs font-medium rounded {{ $contract->jenis_kontrak == 'Kontrak' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $contract->jenis_kontrak }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                {{ $contract->opd?->singkatan ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-slate-700 line-clamp-2" title="{{ $contract->nama_penyedia }}">{{ $contract->nama_penyedia }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <span class="text-sm font-semibold text-emerald-600">{{ $contract->getNilaiKontrakFormatted() }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                                {{ $contract->tahun_anggaran }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($contract->hasFile())
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                PDF
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Tidak
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('contracts.show', $contract) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors ui-button btn-secondary table-action-button"
                                   title="Lihat Detail">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat
                                </a>
                                @if($contract->hasFile())
                                <a href="{{ route('contracts.download', $contract) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded transition-colors ui-button btn-secondary table-action-button"
                                   title="Unduh PDF">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Unduh
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-base font-medium text-slate-800 mb-1">Tidak ada kontrak ditemukan</h3>
                                <p class="text-sm text-slate-500">Coba ubah filter pencarian atau tambahkan kontrak baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $contracts->withQueryString()->links() }}
    </div>
</x-app-layout>

