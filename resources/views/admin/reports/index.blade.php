<x-app-layout>
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Kontrak</h1>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg  p-6 mb-6 ui-card">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Filter Laporan</h2>
        <form action="{{ route('admin.reports.index') }}" method="GET" class="ui-filter grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <div>
                <div class="min-w-0"><label for="filter-tahun">Tahun anggaran</label><select id="filter-tahun" name="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm ui-input">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select></div>
            </div>
            <div>
                <div class="min-w-0"><label for="filter-opd_id">OPD</label><select id="filter-opd_id" name="opd_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm ui-input">
                    <option value="">Semua OPD</option>
                    @foreach($opds as $opd)
                    <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>{{ $opd->singkatan ?? $opd->nama }}</option>
                    @endforeach
                </select></div>
            </div>
            <div>
                <div class="min-w-0"><label for="filter-jenis">Jenis kontrak</label><select id="filter-jenis" name="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm ui-input">
                    <option value="">Semua Jenis</option>
                    @foreach(\App\Models\Contract::JENIS_OPTIONS as $value => $label)
                    <option value="{{ $value }}" {{ request('jenis') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select></div>
            </div>
            <div>
                <div class="min-w-0"><label for="filter-sumber_dana">Sumber dana</label><select id="filter-sumber_dana" name="sumber_dana" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm ui-input">
                    <option value="">Semua Sumber Dana</option>
                    @foreach(\App\Models\Contract::SUMBER_DANA_OPTIONS as $value => $label)
                    <option value="{{ $value }}" {{ request('sumber_dana') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select></div>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm ui-button btn-primary">Filter</button>
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm ui-button btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg  p-4 ui-card">
            <p class="text-sm text-gray-500">Total Kontrak</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_kontrak']) }}</p>
        </div>
        <div class="bg-white rounded-lg  p-4 ui-card">
            <p class="text-sm text-gray-500">Total Nilai</p>
            <p class="text-xl font-bold text-green-600">Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg  p-4 ui-card">
            <p class="text-sm text-gray-500">Kontrak</p>
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['kontrak_count']) }}</p>
        </div>
        <div class="bg-white rounded-lg  p-4 ui-card">
            <p class="text-sm text-gray-500">SPK</p>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['spk_count']) }}</p>
        </div>
        <div class="bg-white rounded-lg  p-4 ui-card">
            <p class="text-sm text-gray-500">Dengan Dokumen</p>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['dengan_dokumen']) }}</p>
        </div>
        <div class="bg-white rounded-lg  p-4 ui-card">
            <p class="text-sm text-gray-500">Tanpa Dokumen</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['tanpa_dokumen']) }}</p>
        </div>
    </div>

    <!-- Export Button -->
    <div class="bg-white rounded-lg  p-6 mb-6 ui-card">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Export ke Excel</h2>
                <p class="text-sm text-gray-500">Download rekapan data kontrak sesuai filter yang dipilih</p>
            </div>
            <a href="{{ route('admin.reports.export', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 ui-button btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download Excel
            </a>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="bg-white rounded-lg  overflow-hidden ui-card">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Preview Data ({{ $contracts->total() }} kontrak)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Kontrak</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kontrak</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">OPD</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contracts as $index => $contract)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $contracts->firstItem() + $index }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $contract->nomor_kontrak }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($contract->nama_kontrak, 40) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $contract->opd?->singkatan ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $contract->jenis_kontrak == 'Kontrak' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $contract->jenis_kontrak ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $contract->getNilaiKontrakFormatted() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $contract->tahun_anggaran }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada data kontrak</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $contracts->links() }}
        </div>
        @endif
    </div>
</div>
</x-app-layout>

