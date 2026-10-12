<x-app-layout>
    <x-slot name="title">Pengguna</x-slot>

    <div class="page-header mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Pengguna</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white  rounded-lg p-4 mb-6 ui-card">
        <form method="GET" class="ui-filter grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="min-w-0"><label for="filter-search">Pencarian</label><input type="text" id="filter-search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm ui-input"></div>
            <div class="min-w-0"><label for="filter-status">Status verifikasi</label><select id="filter-status" name="status" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm ui-input">
                <option value="">Semua Status</option>
                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Belum Terverifikasi</option>
            </select></div>
            <div class="min-w-0"><label for="filter-role">Peran</label><select id="filter-role" name="role" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm ui-input">
                <option value="">Semua peran</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Pengguna</option>
            </select></div>
            <div class="ui-filter-actions">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm ui-button btn-primary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm ui-button btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white  rounded-lg overflow-hidden ui-card">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OPD</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terdaftar</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->opd)
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ $user->opd->singkatan ?? $user->opd->nama }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->isAdmin() ? 'Admin' : 'Petugas' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->isVerified())
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Terverifikasi</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-3 text-sm font-medium"><div class="table-actions">
                        <a data-dialog-open="user-dialog" data-action="{{ route('admin.users.update', $user) }}" data-method="PUT" data-title="Ubah pengguna" data-values="{{ json_encode(['_record_id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'opd_id' => $user->opd_id]) }}" href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Ubah</a>
                        @if($user->id !== auth()->id())
                            @if(!$user->isVerified() && !$user->isAdmin())
                            <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">Verifikasi</button>
                            </form>
                            @elseif(!$user->isAdmin())
                            <form action="{{ route('admin.users.unverify', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900">Batalkan verifikasi</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.users.changeRole', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="role" value="{{ $user->isAdmin() ? 'user' : 'admin' }}">
                                <button type="submit" class="text-blue-600 hover:text-blue-900">
                                    {{ $user->isAdmin() ? 'Jadikan petugas' : 'Jadikan admin' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        @else
                            <span class="text-gray-400">Anda</span>
                        @endif
                    </div></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Pengguna tidak ditemukan. Ubah pencarian atau reset filter.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->withQueryString()->links() }}
    </div>

    @php $record = $errors->any() && old('_modal') === 'user' ? $users->firstWhere('id', (int) old('_record_id')) : null; @endphp
    <dialog id="user-dialog" class="ui-dialog" aria-labelledby="user-dialog-title" @if($record) data-auto-open @endif>
        <div class="ui-dialog-header"><div><h2 id="user-dialog-title" data-dialog-title>Ubah pengguna</h2><p>Kolom bertanda * wajib diisi.</p></div><button class="ui-close" type="button" data-dialog-close aria-label="Tutup form pengguna">&times;</button></div>
        <form action="{{ $record ? route('admin.users.update', $record) : '' }}" method="POST">
            @csrf @method('PUT')<input type="hidden" name="_modal" value="user"><input type="hidden" name="_record_id" value="{{ old('_record_id') }}">
            @include('admin.users.partials.fields', ['record' => $record])
            <div class="dialog-actions"><button class="ui-button btn-secondary" type="button" data-dialog-close>Batal</button><button class="ui-button btn-primary" type="submit">Simpan perubahan</button></div>
        </form>
    </dialog>
</x-app-layout>

