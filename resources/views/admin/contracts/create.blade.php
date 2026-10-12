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

    <div id="upload-feedback" class="ui-alert ui-alert-error hidden" role="alert" tabindex="-1"></div>
    <div id="upload-progress-modal" class="ui-card p-5 mb-6 hidden" role="status" aria-live="polite">
        <p id="upload-progress-label" class="text-sm font-semibold mb-3">Mengunggah dokumen…</p>
        <div class="bg-slate-200 rounded-full h-2 overflow-hidden"><div id="upload-progress-bar" class="bg-blue-600 h-2" role="progressbar" aria-label="Progres unggahan" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width:0%"></div></div>
        <p class="text-xs text-slate-500 mt-2"><span id="upload-progress-percent">0</span>% · Jangan tutup halaman selama penyimpanan.</p>
    </div>

    <form action="{{ route('admin.contracts.store') }}" method="POST" enctype="multipart/form-data" id="contract-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white rounded-xl  border border-slate-200 overflow-visible ui-card">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
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
                                        class="flex-1 px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nomor_kontrak') border-red-400 ring-1 ring-red-400 @enderror ui-input">
                                    <button type="button" id="btn-validate-contract" onclick="validateContractNumber()"
                                        class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2 ui-button btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Validasi</span>
                                    </button>
                                </div>
                                <div id="validation-result" role="status" aria-live="polite" class="mt-2 hidden"></div>
                                @error('nomor_kontrak')<p class="mt-1.5 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="tahun_anggaran" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Tahun Anggaran <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="tahun_anggaran" id="tahun_anggaran" value="{{ old('tahun_anggaran', date('Y')) }}" min="2000" max="2100" required
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('tahun_anggaran') border-red-400 ring-1 ring-red-400 @enderror ui-input">
                                @error('tahun_anggaran')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="nama_kontrak" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Nama Kontrak <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_kontrak" id="nama_kontrak" value="{{ old('nama_kontrak') }}" required
                                placeholder="Masukkan nama/judul kontrak"
                                class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nama_kontrak') border-red-400 ring-1 ring-red-400 @enderror ui-input">
                            @error('nama_kontrak')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="jenis_kontrak" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Jenis Kontrak <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_kontrak" id="jenis_kontrak" required
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('jenis_kontrak') border-red-400 ring-1 ring-red-400 @enderror ui-input">
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
                                    <input type="text" id="opd_search" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="opd_dropdown" autocomplete="off"
                                        placeholder="Ketik nama/singkatan OPD..."
                                        class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all ui-input">
                                    <input type="hidden" name="opd_id" id="opd_id" value="{{ old('opd_id') }}">
                                    <div id="opd_dropdown" role="listbox" aria-label="Hasil pencarian OPD" class="absolute z-[100] w-full mt-1 bg-white border border-slate-300 rounded-lg  max-h-60 overflow-y-auto hidden ui-card">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Penyedia & Nilai -->
                <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
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
                                class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nama_penyedia') border-red-400 ring-1 ring-red-400 @enderror ui-input">
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
                                        class="w-full pl-12 pr-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nilai_kontrak') border-red-400 ring-1 ring-red-400 @enderror ui-input">
                                </div>
                                @error('nilai_kontrak')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="sumber_dana" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Sumber Dana <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                                </label>
                                <select name="sumber_dana" id="sumber_dana"
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all ui-input">
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
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all ui-input">
                            </div>
                        </div>

                        <div>
                            <label for="lokasi" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Lokasi <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                            </label>
                            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}"
                                placeholder="Contoh: Jl. Sudirman No. 123, Kota Jayapura"
                                class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all ui-input">
                        </div>
                    </div>
                </div>

                <!-- Periode Kontrak -->
                <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
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
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('tanggal_kontrak') border-red-400 ring-1 ring-red-400 @enderror ui-input">
                                @error('tanggal_kontrak')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Tanggal Mulai <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                                </label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all ui-input">
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Tanggal Selesai <span class="text-slate-400 font-normal italic text-xs">(Opsional)</span>
                                </label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all ui-input">
                                @error('tanggal_selesai')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Document & Notes -->
            <div class="space-y-6">
                <!-- Upload Dokumen -->
                <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-800">Dokumen Kontrak</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Unggah hasil pindai kontrak</p>
                    </div>
                    <div class="p-6">
                        <div class="upload-picker">
                            <div class="mx-auto w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-700 mb-1">Pilih dokumen kontrak</p>
                            <p class="text-xs text-slate-500">Gunakan tombol untuk memilih file PDF.</p>
                            <p class="text-xs text-slate-400 mt-3">Format: PDF • Maks: 30MB</p>
                            <input type="file" name="dokumen" id="dokumen" accept="application/pdf,.pdf" aria-label="Pilih dokumen PDF" class="block" onchange="updateFileName(this)">
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
                <div class="bg-white rounded-xl  border border-slate-200 overflow-hidden ui-card">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-800">Keterangan</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Catatan tambahan</p>
                    </div>
                    <div class="p-6">
                        <textarea name="keterangan" id="keterangan" aria-label="Keterangan kontrak" rows="5"
                            placeholder="Tambahkan catatan atau keterangan lainnya..."
                            class="w-full px-4 py-3 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none ui-input">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl border border-slate-200 p-6">
                    <div class="space-y-3">
                        <button type="submit" id="btn-submit-contract" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg  hover: transition-all flex items-center justify-center ui-button btn-primary">
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

        function escapeContractText(value) {
            const element = document.createElement('span');
            element.textContent = String(value ?? '');
            return element.innerHTML;
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
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ nomor_kontrak: nomorKontrak })
                });

                if (!response.ok) throw new Error('Validasi gagal');
                const data = await response.json();
                if (document.getElementById('nomor_kontrak').value.trim() !== nomorKontrak) return;

                if (data.valid) {
                    resultDiv.innerHTML = `
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-700 font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                ${escapeContractText(data.message)}
                            </p>
                        </div>
                    `;
                } else {
                    let details = '';
                    if (data.existing) {
                        details = `
                            <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                                <div><strong>Nama Kontrak:</strong> ${escapeContractText(data.existing.nama_kontrak)}</div>
                                <div><strong>Penyedia:</strong> ${escapeContractText(data.existing.nama_penyedia)}</div>
                                <div><strong>OPD:</strong> ${escapeContractText(data.existing.opd)}</div>
                                <div><strong>Tahun:</strong> ${escapeContractText(data.existing.tahun_anggaran)}</div>
                            </div>
                        `;
                    }
                    resultDiv.innerHTML = `
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-700 font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                ${escapeContractText(data.message)}
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

        document.getElementById('nomor_kontrak').addEventListener('input', () => {
            document.getElementById('validation-result').classList.add('hidden');
        });

        const opds = @json($opds);
        const opdSearch = document.getElementById('opd_search');
        const opdId = document.getElementById('opd_id');
        const opdDropdown = document.getElementById('opd_dropdown');

        opdSearch.addEventListener('input', function() {
            opdId.value = '';
            opdSearch.setAttribute('aria-expanded', 'false');
            const query = this.value.toLowerCase().trim();

            if (query.length < 3) {
                opdDropdown.classList.add('hidden');
            opdSearch.setAttribute('aria-expanded', 'false');
                opdDropdown.innerHTML = '';
                return;
            }

            const filtered = opds.filter(opd =>
                opd.nama.toLowerCase().includes(query) ||
                (opd.singkatan ?? '').toLowerCase().includes(query) ||
                opd.kode.toLowerCase().includes(query)
            );

            if (filtered.length === 0) {
                opdDropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">Tidak ada hasil</div>';
                opdDropdown.classList.remove('hidden');
            opdSearch.setAttribute('aria-expanded', 'true');
                return;
            }

            opdDropdown.replaceChildren();
            filtered.forEach(opd => {
                const option = document.createElement('button');
                option.type = 'button';
                option.setAttribute('role', 'option');
                option.setAttribute('aria-selected', 'false');
                option.className = 'block w-full text-left px-4 py-3 hover:bg-blue-50 cursor-pointer text-sm border-b border-gray-100 last:border-0';
                const title = document.createElement('div');
                title.className = 'font-medium text-slate-700';
                title.textContent = opd.singkatan ?? '';
                const description = document.createElement('div');
                description.className = 'text-xs text-slate-500';
                description.textContent = opd.nama;
                option.append(title, description);
                option.addEventListener('click', () => selectOpd(opd.id, opd.singkatan ?? '', opd.nama));
                opdDropdown.append(option);
            });

            opdDropdown.classList.remove('hidden');
            opdSearch.setAttribute('aria-expanded', 'true');
        });

        opdSearch.addEventListener('keydown', event => {
            if (event.key === 'ArrowDown' && !opdDropdown.classList.contains('hidden')) {
                event.preventDefault();
                opdDropdown.querySelector('button')?.focus();
            }
        });
        opdDropdown.addEventListener('keydown', event => {
            const options = Array.from(opdDropdown.querySelectorAll('button'));
            const index = options.indexOf(document.activeElement);
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                options[(index + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length]?.focus();
            } else if (event.key === 'Escape') {
                opdDropdown.classList.add('hidden');
                opdSearch.setAttribute('aria-expanded', 'false');
                opdSearch.focus();
            }
        });

        function selectOpd(id, singkatan, nama) {
            opdId.value = id;
            opdSearch.value = `${singkatan} - ${nama}`;
            opdDropdown.classList.add('hidden');
            opdSearch.setAttribute('aria-expanded', 'false');
            opdSearch.focus();
        }

        document.addEventListener('click', function(e) {
            if (!opdSearch.contains(e.target) && !opdDropdown.contains(e.target)) {
                opdDropdown.classList.add('hidden');
            opdSearch.setAttribute('aria-expanded', 'false');
            }
        });

        const oldOpdId = "{{ old('opd_id') }}";
        if (oldOpdId) {
            const selectedOpd = opds.find(opd => opd.id == oldOpdId);
            if (selectedOpd) {
                opdSearch.value = `${selectedOpd.singkatan} - ${selectedOpd.nama}`;
            }
        }

        const contractForm = document.getElementById('contract-form');
        const progressPanel = document.getElementById('upload-progress-modal');
        const progressBar = document.getElementById('upload-progress-bar');
        const progressPercent = document.getElementById('upload-progress-percent');
        const progressLabel = document.getElementById('upload-progress-label');
        const submitButton = document.getElementById('btn-submit-contract');
        const feedback = document.getElementById('upload-feedback');

        contractForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (submitButton.disabled || !contractForm.reportValidity()) return;
            feedback.classList.add('hidden');
            contractForm.querySelectorAll('[data-upload-error]').forEach(error => error.remove());
            contractForm.querySelectorAll('[aria-invalid]').forEach(field => {
                field.removeAttribute('aria-invalid');
                if (field.getAttribute('aria-describedby')?.startsWith('upload-error-')) field.removeAttribute('aria-describedby');
            });
            progressPanel.classList.remove('hidden');
            progressPanel.scrollIntoView({ block: 'start' });
            progressBar.style.width = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
            progressPercent.textContent = '0';
            progressLabel.textContent = 'Mengunggah data kontrak…';
            submitButton.disabled = true;
            contractForm.setAttribute('aria-busy', 'true');

            function fail(message, errors = {}) {
                progressPanel.classList.add('hidden');
                submitButton.disabled = false;
                contractForm.removeAttribute('aria-busy');
                feedback.textContent = message;
                feedback.classList.remove('hidden');
                Object.entries(errors).forEach(([name, messages]) => {
                    const field = contractForm.elements.namedItem(name);
                    if (!field) return;
                    const error = document.createElement('p');
                    error.dataset.uploadError = '';
                    error.id = 'upload-error-' + name;
                    error.className = 'mt-1.5 text-xs text-red-600';
                    error.textContent = messages.join(' ');
                    field.setAttribute('aria-invalid', 'true');
                    field.setAttribute('aria-describedby', error.id);
                    const container = field.parentElement.classList.contains('flex') ? field.parentElement.parentElement : field.parentElement;
                    container.append(error);
                });
                feedback.focus();
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', contractForm.action, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.timeout = 120000;
            xhr.upload.addEventListener('progress', event => {
                if (!event.lengthComputable) return;
                const percent = Math.round(event.loaded / event.total * 100);
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', String(percent));
                progressPercent.textContent = percent;
            });
            xhr.upload.addEventListener('load', () => progressLabel.textContent = 'Unggahan selesai. Menyimpan kontrak di server…');
            xhr.addEventListener('load', () => {
                let data;
                try { data = JSON.parse(xhr.responseText); } catch { data = {}; }
                if (xhr.status === 201 && data.redirect) {
                    window.location.assign(data.redirect);
                    return;
                }
                const message = xhr.status === 422 ? 'Kontrak belum disimpan. Periksa kolom yang ditandai.'
                    : xhr.status === 419 ? 'Sesi telah berakhir. Buka halaman masuk pada tab baru, lalu coba kembali.'
                    : xhr.status === 413 ? 'Ukuran unggahan terlalu besar. Pilih PDF yang lebih kecil.'
                    : 'Kontrak belum dapat disimpan. Periksa koneksi lalu coba kembali.';
                fail(message, data.errors || {});
            });
            xhr.addEventListener('error', () => fail('Koneksi terputus. Data form tetap tersedia; periksa koneksi lalu coba kembali.'));
            xhr.addEventListener('timeout', () => fail('Penyimpanan membutuhkan waktu lebih lama. Periksa daftar kontrak sebelum mencoba kembali.'));
            xhr.send(new FormData(contractForm));
        });
    </script>
</x-app-layout>

