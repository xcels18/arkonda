<x-app-layout>
    <x-slot name="title">Edit OPD</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit OPD</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.opds.update', $opd) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700">Kode OPD</label>
                    <input type="text" id="kode" name="kode" value="{{ old('kode', $opd->kode) }}" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('kode') border-red-500 @enderror">
                    @error('kode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama OPD</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $opd->nama) }}" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nama') border-red-500 @enderror">
                    @error('nama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="singkatan" class="block text-sm font-medium text-gray-700">Singkatan</label>
                    <input type="text" id="singkatan" name="singkatan" value="{{ old('singkatan', $opd->singkatan) }}" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('singkatan') border-red-500 @enderror">
                    @error('singkatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.opds.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        Perbarui
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
