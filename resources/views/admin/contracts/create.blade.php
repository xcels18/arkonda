<x-app-layout>
    <x-slot name="title">Tambah Kontrak</x-slot>

    <!-- Header Section -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm">
                <li><a href="{{ route('admin.contracts.index') }}" class="text-slate-500 hover:text-blue-600 transition-colors">Kelola Kontrak</a></li>
                <li class="text-slate-400">/</li>
                <li class="text-slate-700 font-medium">Tambah Baru</li>
            </ol>
        </nav>
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Tambah Kontrak Baru</h1>
        <p class="mt-2 text-base text-slate-500">Lengkapi informasi kontrak dan unggah dokumen pendukung</p>
    </div>

    <form action="{{ route('admin.contracts.store') }}" method="POST" enctype="multipart/form-data" id="contract-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-800">Informasi Dasar</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Data utama kontrak</p>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="nomor_kontrak" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Nomor Kontrak <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" name="nomor_kontrak" id="nomor_kontrak" value="{{ old('nomor_kontrak') }}" required
                                        placeholder="Contoh: 001/SPK/2026"
                                        class="flex-1 px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nomor_kontrak') border-red-400 ring-1 ring-red-400 @enderror">
                                    <button type="button" id="btn-validate-contract" onclick="validateContractNumber()"
                                        class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Validasi</span>
                                    </button>
                                </div>
                                <div id="validation-result" class="mt-2 hidden"></div>
                                @error('nomor_kontrak')<p class="mt-1.5 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="tahun_anggaran" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Tahun Anggaran <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="tahun_anggaran" id="tahun_anggaran" value="{{ old('tahun_anggaran', date('Y')) }}" min="2000" max="2100" required
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('tahun_anggaran') border-red-400 ring-1 ring-red-400 @enderror">
                                @error('tahun_anggaran')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="nama_kontrak" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Nama Kontrak <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_kontrak" id="nama_kontrak" value="{{ old('nama_kontrak') }}" required
                                placeholder="Masukkan nama/judul kontrak"
                                class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nama_kontrak') border-red-400 ring-1 ring-red-400 @enderror">
                            @error('nama_kontrak')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="jenis_kontrak" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Jenis Kontrak <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_kontrak" id="jenis_kontrak" required
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('jenis_kontrak') border-red-400 ring-1 ring-red-400 @enderror">
                                    <option value="">— Pilih Jenis —</option>
                                    @foreach(\App\Models\Contract::JENIS_OPTIONS as $value => $label)
                                    <option value="{{ $value }}" {{ old('jenis_kontrak') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_kontrak')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="opd_search" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    OPD <span class="text-slate-400 font-normal italic text-xs">(Opsional - ketik min. 3 huruf)</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="opd_search" autocomplete="off"
                                        placeholder="Ketik nama/singkatan OPD..."
                                        class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <input type="hidden" name="opd_id" id="opd_id" value="{{ old('opd_id') }}">
                                    <div id="opd_dropdown" class="absolute z-[100] w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Penyedia & Nilai -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-800">Penyedia & Nilai</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Informasi rekanan dan nilai kontrak</p>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="nama_penyedia" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Nama Penyedia <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_penyedia" id="nama_penyedia" value="{{ old('nama_penyedia') }}" required
                                placeholder="Nama perusahaan/CV/PT penyedia"
                                class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nama_penyedia') border-red-400 ring-1 ring-red-400 @enderror">
                            @error('nama_penyedia')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label for="nilai_kontrak" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Nilai Kontrak <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                                    <input type="number" name="nilai_kontrak" id="nilai_kontrak" value="{{ old('nilai_kontrak') }}" min="0" step="0.01" required
                                        placeholder="0"
                                        class="w-full pl-12 pr-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nilai_kontrak') border-red-400 ring-1 ring-red-400 @enderror">
                                </div>
                                @error('nilai_kontrak')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="sumber_dana" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Sumber Dana <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                                </label>
                                <select name="sumber_dana" id="sumber_dana"
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">— Pilih Sumber Dana —</option>
                                    @foreach(\App\Models\Contract::SUMBER_DANA_OPTIONS as $value => $label)
                                    <option value="{{ $value }}" {{ old('sumber_dana') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="bidang" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Bidang <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                                </label>
                                <input type="text" name="bidang" id="bidang" value="{{ old('bidang', 'Aset') }}"
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div>
                            <label for="lokasi" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Lokasi <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                            </label>
                            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}"
                                placeholder="Contoh: Jl. Sudirman No. 123, Kota Jayapura"
                                class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>

                <!-- Periode Kontrak -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-800">Periode Kontrak</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Tanggal pelaksanaan kontrak</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label for="tanggal_kontrak" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Tanggal Kontrak <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_kontrak" id="tanggal_kontrak" value="{{ old('tanggal_kontrak') }}" required
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('tanggal_kontrak') border-red-400 ring-1 ring-red-400 @enderror">
                                @error('tanggal_kontrak')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Tanggal Mulai <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                                </label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Tanggal Selesai <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                                </label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                @error('tanggal_selesai')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Document & Notes -->
            <div class="space-y-6">
                <!-- Upload Dokumen -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-800">Dokumen Kontrak</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Upload file scan kontrak</p>
                    </div>
                    <div class="p-6">
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-blue-400 hover:bg-blue-50/50 transition-all cursor-pointer" onclick="document.getElementById('dokumen').click()">
                            <div class="mx-auto w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-700 mb-1">Klik untuk upload file</p>
                            <p class="text-xs text-slate-500">atau drag & drop file di sini</p>
                            <p class="text-xs text-slate-400 mt-3">Format: PDF • Maks: 30MB</p>
                            <input type="file" name="dokumen" id="dokumen" accept=".pdf" class="hidden" onchange="updateFileName(this)">
                        </div>
                        <div id="file-name" class="mt-3 text-sm text-slate-600 hidden">
                            <span class="flex items-center text-green-600">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span id="selected-file"></span>
                            </span>
                        </div>
                        @error('dokumen')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-800">Keterangan</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Catatan tambahan</p>
                    </div>
                    <div class="p-6">
                        <textarea name="keterangan" id="keterangan" rows="5"
                            placeholder="Tambahkan catatan atau keterangan lainnya..."
                            class="w-full px-4 py-3 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl border border-slate-200 p-6">
                    <div class="space-y-3">
                        <button type="submit" id="btn-submit-contract" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm hover:shadow transition-all flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Kontrak
                        </button>
                        <a href="{{ route('admin.contracts.index') }}" class="w-full px-6 py-3 bg-white hover:bg-slate-50 text-slate-700 font-medium rounded-lg border border-slate-300 transition-all flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>
                    </div>
                    <p class="text-xs text-slate-500 text-center mt-4">
                        <span class="text-red-500">*</span> Menandakan field wajib diisi
                    </p>
                </div>
            </div>
        </div>
    </form>

    <!-- Upload Progress Modal -->
    <div id="upload-progress-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center mb-4">
                <svg class="animate-spin h-6 w-6 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <h3 id="upload-progress-label" class="text-base font-semibold text-slate-800">Menyimpan Kontrak...</h3>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                <div id="upload-progress-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-150" style="width: 0%"></div>
            </div>
            <p class="text-sm text-slate-500 mt-2 text-center"><span id="upload-progress-percent">0</span>%</p>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileNameDiv = document.getElementById('file-name');
            const selectedFile = document.getElementById('selected-file');
            if (input.files && input.files[0]) {
                selectedFile.textContent = input.files[0].name;
                fileNameDiv.classList.remove('hidden');
            } else {
                fileNameDiv.classList.add('hidden');
            }
        }

        async function validateContractNumber() {
            const nomorKontrak = document.getElementById('nomor_kontrak').value.trim();
            const resultDiv = document.getElementById('validation-result');
            const button = document.getElementById('btn-validate-contract');

            if (!nomorKontrak) {
                resultDiv.innerHTML = '<p class="text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Masukkan nomor kontrak terlebih dahulu</p>';
                resultDiv.classList.remove('hidden');
                return;
            }

            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="ml-2">Memeriksa...</span>';

            try {
                const response = await fetch('{{ route("admin.contracts.validate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ nomor_kontrak: nomorKontrak })
                });

                const data = await response.json();

                if (data.valid) {
                    resultDiv.innerHTML = `
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-700 font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                ${data.message}
                            </p>
                        </div>
                    `;
                } else {
                    let details = '';
                    if (data.existing) {
                        details = `
                            <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                                <div><strong>Nama Kontrak:</strong> ${data.existing.nama_kontrak}</div>
                                <div><strong>Penyedia:</strong> ${data.existing.nama_penyedia}</div>
                                <div><strong>OPD:</strong> ${data.existing.opd}</div>
                                <div><strong>Tahun:</strong> ${data.existing.tahun_anggaran}</div>
                            </div>
                        `;
                    }
                    resultDiv.innerHTML = `
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-700 font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                ${data.message}
                            </p>
                            ${details}
                        </div>
                    `;
                }

                resultDiv.classList.remove('hidden');
            } catch (error) {
                resultDiv.innerHTML = '<p class="text-sm text-red-600">Terjadi kesalahan saat validasi</p>';
                resultDiv.classList.remove('hidden');
            } finally {
                button.disabled = false;
                button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Validasi</span>';
            }
        }

        const opds = @json($opds);
        const opdSearch = document.getElementById('opd_search');
        const opdId = document.getElementById('opd_id');
        const opdDropdown = document.getElementById('opd_dropdown');

        opdSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            if (query.length < 3) {
                opdDropdown.classList.add('hidden');
                opdDropdown.innerHTML = '';
                return;
            }

            const filtered = opds.filter(opd => 
                opd.nama.toLowerCase().includes(query) || 
                opd.singkatan.toLowerCase().includes(query) ||
                opd.kode.toLowerCase().includes(query)
            );

            if (filtered.length === 0) {
                opdDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">Tidak ada hasil</div>';
                opdDropdown.classList.remove('hidden');
                return;
            }

            opdDropdown.innerHTML = filtered.map(opd => `
                <div class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer text-sm transition-colors border-b border-slate-100 last:border-0" 
                     onclick="selectOpd(${opd.id}, '${opd.singkatan}', '${opd.nama}')">
                    <div class="font-medium text-slate-700">${opd.singkatan}</div>
                    <div class="text-xs text-slate-500">${opd.nama}</div>
                </div>
            `).join('');
            
            opdDropdown.classList.remove('hidden');
        });

        function selectOpd(id, singkatan, nama) {
            opdId.value = id;
            opdSearch.value = `${singkatan} - ${nama}`;
            opdDropdown.classList.add('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!opdSearch.contains(e.target) && !opdDropdown.contains(e.target)) {
                opdDropdown.classList.add('hidden');
            }
        });

        const oldOpdId = "{{ old('opd_id') }}";
        if (oldOpdId) {
            const selectedOpd = opds.find(opd => opd.id == oldOpdId);
            if (selectedOpd) {
                opdSearch.value = `${selectedOpd.singkatan} - ${selectedOpd.nama}`;
            }
        }

        // Upload progress modal handling
        const contractForm = document.getElementById('contract-form');
        const progressModal = document.getElementById('upload-progress-modal');
        const progressBar = document.getElementById('upload-progress-bar');
        const progressPercent = document.getElementById('upload-progress-percent');
        const progressLabel = document.getElementById('upload-progress-label');
        const submitButton = document.getElementById('btn-submit-contract');

        contractForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Guard: if required fields are not filled, let the browser show
            // its native validation messages instead of showing the modal.
            if (!contractForm.checkValidity()) {
                contractForm.reportValidity();
                return;
            }

            const formData = new FormData(contractForm);

            let displayedPercent = 0;
            let uploadPercent = 0;
            let uploadDone = false;
            let finished = false;

            function setDisplay(percent, label) {
                displayedPercent = percent;
                progressBar.style.width = percent + '%';
                progressPercent.textContent = percent;
                if (label) {
                    progressLabel.textContent = label;
                }
            }

            setDisplay(0, 'Menyimpan Kontrak...');
            progressModal.classList.remove('hidden');
            progressModal.classList.add('flex');
            submitButton.disabled = true;

            // Smoothly animate the displayed percentage toward the target percentage,
            // so the bar always moves gradually even for very small/fast uploads.
            const smoothTimer = setInterval(function () {
                let target = uploadDone ? 95 : uploadPercent;
                if (finished) {
                    target = 100;
                }
                if (displayedPercent < target) {
                    displayedPercent = Math.min(target, displayedPercent + 2);
                    progressBar.style.width = displayedPercent + '%';
                    progressPercent.textContent = displayedPercent;
                }
                if (finished && displayedPercent >= 100) {
                    clearInterval(smoothTimer);
                }
            }, 30);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', contractForm.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function (event) {
                if (event.lengthComputable) {
                    uploadPercent = Math.round((event.loaded / event.total) * 95);
                } else {
                    uploadPercent = 50;
                }
            });

            xhr.upload.addEventListener('load', function () {
                uploadDone = true;
                progressLabel.textContent = 'Memproses di server...';
            });

            xhr.addEventListener('load', function () {
                finished = true;
                setDisplay(100, 'Selesai!');

                setTimeout(function () {
                    if (xhr.responseURL && (xhr.status === 200 || xhr.status === 201)) {
                        // Followed redirect successfully, load the final page
                        window.location.href = xhr.responseURL;
                    } else {
                        // Validation error or other issue: render the returned HTML (e.g. redirect back with errors)
                        document.open();
                        document.write(xhr.responseText);
                        document.close();
                    }
                }, 400);
            });

            xhr.addEventListener('error', function () {
                clearInterval(smoothTimer);
                progressModal.classList.add('hidden');
                progressModal.classList.remove('flex');
                submitButton.disabled = false;
                alert('Terjadi kesalahan saat mengunggah. Silakan coba lagi.');
            });

            xhr.send(formData);
        });
    </script>
</x-app-layout>

