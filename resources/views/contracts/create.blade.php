<x-app-layout>
    <x-slot name="title">Tambah Kontrak</x-slot>

    <!-- Header Section -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm">
                <li><a href="{{ route('contracts.index') }}" class="text-slate-500 hover:text-blue-600 transition-colors">Daftar Kontrak</a></li>
                <li class="text-slate-400">/</li>
                <li class="text-slate-700 font-medium">Tambah Baru</li>
            </ol>
        </nav>
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Tambah Kontrak Baru</h1>
        <p class="mt-2 text-base text-slate-500">
            <span class="inline-flex items-center px-2.5 py-1 text-sm font-medium rounded-lg bg-blue-100 text-blue-800">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ $userOpd->singkatan ? $userOpd->singkatan . ' - ' : '' }}{{ $userOpd->nama }}
            </span>
        </p>
    </div>

    <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
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
                                <input type="text" name="nomor_kontrak" id="nomor_kontrak" value="{{ old('nomor_kontrak') }}" required
                                    placeholder="Contoh: 001/SPK/2026"
                                    class="w-full px-4 py-2.5 text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nomor_kontrak') border-red-400 ring-1 ring-red-400 @enderror">
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
                                <input type="text" name="bidang" id="bidang" value="{{ old('bidang') }}"
                                    placeholder="Contoh: Aset, Infrastruktur"
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
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm hover:shadow transition-all flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Kontrak
                        </button>
                        <a href="{{ route('contracts.index') }}" class="w-full px-6 py-3 bg-white hover:bg-slate-50 text-slate-700 font-medium rounded-lg border border-slate-300 transition-all flex items-center justify-center">
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
    </script>
</x-app-layout>

