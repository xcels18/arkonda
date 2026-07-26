<x-app-layout>
    <x-slot name="title">Edit Kontrak</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Kontrak</h1>
        <p class="text-gray-600">{{ $contract->nomor_kontrak }}</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.contracts.update', $contract) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nomor_kontrak" class="block text-sm font-medium text-gray-700">Nomor Kontrak *</label>
                    <div class="flex gap-2">
                        <input type="text" name="nomor_kontrak" id="nomor_kontrak" value="{{ old('nomor_kontrak', $contract->nomor_kontrak) }}" required
                            class="flex-1 mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nomor_kontrak') border-red-500 @enderror">
                        <button type="button" id="btn-validate-contract" onclick="validateContractNumber()"
                            class="mt-1 px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-all flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Validasi</span>
                        </button>
                    </div>
                    <div id="validation-result" class="mt-2 hidden"></div>
                    @error('nomor_kontrak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="tahun_anggaran" class="block text-sm font-medium text-gray-700">Tahun Anggaran *</label>
                    <input type="number" name="tahun_anggaran" id="tahun_anggaran" value="{{ old('tahun_anggaran', $contract->tahun_anggaran) }}" min="2000" max="2100" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tahun_anggaran') border-red-500 @enderror">
                    @error('tahun_anggaran')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="nama_kontrak" class="block text-sm font-medium text-gray-700">Nama Kontrak *</label>
                <input type="text" name="nama_kontrak" id="nama_kontrak" value="{{ old('nama_kontrak', $contract->nama_kontrak) }}" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nama_kontrak') border-red-500 @enderror">
                @error('nama_kontrak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama_penyedia" class="block text-sm font-medium text-gray-700">Nama Penyedia *</label>
                    <input type="text" name="nama_penyedia" id="nama_penyedia" value="{{ old('nama_penyedia', $contract->nama_penyedia) }}" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nama_penyedia') border-red-500 @enderror">
                    @error('nama_penyedia')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nilai_kontrak" class="block text-sm font-medium text-gray-700">Nilai Kontrak (Rp) *</label>
                    <input type="number" name="nilai_kontrak" id="nilai_kontrak" value="{{ old('nilai_kontrak', $contract->nilai_kontrak) }}" min="0" step="0.01" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nilai_kontrak') border-red-500 @enderror">
                    @error('nilai_kontrak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="tanggal_kontrak" class="block text-sm font-medium text-gray-700">Tanggal Kontrak *</label>
                    <input type="date" name="tanggal_kontrak" id="tanggal_kontrak" value="{{ old('tanggal_kontrak', $contract->tanggal_kontrak?->format('Y-m-d')) }}" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tanggal_kontrak') border-red-500 @enderror">
                    @error('tanggal_kontrak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', $contract->tanggal_mulai?->format('Y-m-d')) }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai', $contract->tanggal_selesai?->format('Y-m-d')) }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('tanggal_selesai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="opd_search" class="block text-sm font-medium text-gray-700">OPD (ketik min. 3 huruf)</label>
                    <div class="relative">
                        <input type="text" id="opd_search" autocomplete="off"
                            placeholder="Ketik nama/singkatan OPD..."
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <input type="hidden" name="opd_id" id="opd_id" value="{{ old('opd_id', $contract->opd_id) }}">
                        <div id="opd_dropdown" class="absolute z-[100] w-full mt-1 bg-white border border-gray-300 rounded-md shadow-xl max-h-60 overflow-y-auto hidden"></div>
                    </div>
                    @error('opd_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="jenis_kontrak" class="block text-sm font-medium text-gray-700">Jenis Kontrak *</label>
                    <select name="jenis_kontrak" id="jenis_kontrak" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('jenis_kontrak') border-red-500 @enderror">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach(\App\Models\Contract::JENIS_OPTIONS as $value => $label)
                        <option value="{{ $value }}" {{ old('jenis_kontrak', $contract->jenis_kontrak) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenis_kontrak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="sumber_dana" class="block text-sm font-medium text-gray-700">Sumber Dana</label>
                    <select name="sumber_dana" id="sumber_dana"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">— Pilih Sumber Dana —</option>
                        @foreach(\App\Models\Contract::SUMBER_DANA_OPTIONS as $value => $label)
                        <option value="{{ $value }}" {{ old('sumber_dana', $contract->sumber_dana) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="bidang" class="block text-sm font-medium text-gray-700">Bidang</label>
                    <input type="text" name="bidang" id="bidang" value="{{ old('bidang', $contract->bidang) }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $contract->lokasi) }}"
                    placeholder="Contoh: Jl. Sudirman No. 123, Kota Jayapura"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('keterangan', $contract->keterangan) }}</textarea>
            </div>

            <div>
                <label for="dokumen" class="block text-sm font-medium text-gray-700">Dokumen Scan (PDF, max 30MB)</label>
                @if($contract->hasFile())
                <div class="mt-1 mb-2 p-3 bg-gray-50 rounded-md flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $contract->file_name }} ({{ $contract->getFileSizeFormatted() }})</span>
                    <a href="{{ route('contracts.preview', $contract) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Lihat</a>
                </div>
                <p class="text-xs text-gray-500 mb-2">Upload file baru untuk mengganti dokumen yang sudah ada</p>
                @endif
                <input type="file" name="dokumen" id="dokumen" accept=".pdf"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.contracts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>

    <script>
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
            button.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch('{{ route("admin.contracts.validate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        nomor_kontrak: nomorKontrak,
                        contract_id: {{ $contract->id }}
                    })
                });

                const data = await response.json();

                if (data.valid) {
                    resultDiv.innerHTML = `
                        <div class="p-2 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm text-green-700 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
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
                            <div class="mt-1 text-xs text-red-600 bg-red-50 p-2 rounded">
                                <div><strong>Nama:</strong> ${data.existing.nama_kontrak}</div>
                                <div><strong>Penyedia:</strong> ${data.existing.nama_penyedia}</div>
                                <div><strong>OPD:</strong> ${data.existing.opd} | <strong>Tahun:</strong> ${data.existing.tahun_anggaran}</div>
                            </div>
                        `;
                    }
                    resultDiv.innerHTML = `
                        <div class="p-2 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-700 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
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
                opdDropdown.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Tidak ada hasil</div>';
                opdDropdown.classList.remove('hidden');
                return;
            }

            opdDropdown.innerHTML = filtered.map(opd => `
                <div class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm border-b border-gray-100 last:border-0" 
                     onclick="selectOpd(${opd.id}, '${opd.singkatan}', '${opd.nama}')">
                    <div class="font-medium text-gray-700">${opd.singkatan}</div>
                    <div class="text-xs text-gray-500">${opd.nama}</div>
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

        const currentOpdId = opdId.value;
        if (currentOpdId) {
            const selectedOpd = opds.find(opd => opd.id == currentOpdId);
            if (selectedOpd) {
                opdSearch.value = `${selectedOpd.singkatan} - ${selectedOpd.nama}`;
            }
        }
    </script>
</x-app-layout>

