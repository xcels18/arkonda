<x-app-layout>
    <x-slot name="title">Organisasi / OPD</x-slot>
    @php
        $record = $errors->any() && old('_modal') === 'opd' ? $opds->firstWhere('id', (int) old('_record_id')) : null;
        $reopen = $errors->any() && old('_modal') === 'opd';
    @endphp
    <div class="page-header page-header mb-6 flex justify-between items-center gap-4">
        <div><h1>Organisasi / OPD</h1><p class="text-slate-500 mt-1">Kelola organisasi pemilik arsip kontrak.</p></div>
        <a href="{{ route('admin.opds.create') }}" class="ui-button btn-primary" data-dialog-open="opd-dialog" data-action="{{ route('admin.opds.store') }}" data-method="POST" data-title="Tambah OPD">+ Tambah OPD</a>
    </div>
    <form method="GET" class="ui-card ui-filter p-5 mb-6 flex flex-col sm:flex-row gap-3 sm:items-end">
        <div class="flex-1"><label for="opd-search">Cari OPD</label><input id="opd-search" class="ui-input w-full" name="search" value="{{ request('search') }}" placeholder="Kode, nama, atau singkatan"></div>
        <div class="ui-filter-actions"><button class="ui-button btn-primary" type="submit">Cari</button><a class="ui-button btn-secondary" href="{{ route('admin.opds.index') }}">Reset</a></div>
    </form>
    <div class="ui-card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200"><p class="text-xs text-slate-500">{{ $opds->total() }} OPD{{ request('search') ? ' sesuai pencarian' : ' terdaftar' }}</p></div>
        <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Daftar organisasi">
            <table class="min-w-full">
                <thead><tr>@foreach(['Kode', 'Nama OPD', 'Singkatan', 'Tindakan'] as $heading)<th scope="col" class="px-5 py-3 text-left whitespace-nowrap">{{ $heading }}</th>@endforeach</tr></thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($opds as $opd)
                    <tr>
                        <td class="px-5 py-4 whitespace-nowrap font-medium">{{ $opd->kode }}</td>
                        <td class="px-5 py-4 min-w-[240px]">{{ $opd->nama }}</td>
                        <td class="px-5 py-4 text-slate-500">{{ $opd->singkatan ?: '—' }}</td>
                        <td class="px-5 py-3"><div class="table-actions">
                            <a href="{{ route('admin.opds.edit', $opd) }}" class="text-blue-600 font-medium" data-dialog-open="opd-dialog" data-action="{{ route('admin.opds.update', $opd) }}" data-method="PUT" data-title="Ubah OPD" data-values="{{ json_encode(['_record_id' => $opd->id, 'kode' => $opd->kode, 'nama' => $opd->nama, 'singkatan' => $opd->singkatan]) }}" aria-label="Ubah {{ $opd->nama }}">Ubah</a>
                            <form action="{{ route('admin.opds.destroy', $opd) }}" method="POST" onsubmit="return confirm('Hapus OPD ini? Pastikan organisasi tidak lagi digunakan.')">@csrf @method('DELETE')<button class="text-red-600" type="submit" aria-label="Hapus {{ $opd->nama }}">Hapus</button></form>
                        </div></td>
                    </tr>
                    @empty
                    <tr><td colspan="4"><div class="empty-state"><strong>{{ request('search') ? 'OPD tidak ditemukan' : 'Belum ada OPD' }}</strong><p>{{ request('search') ? 'Ubah kata pencarian atau reset filter.' : 'Tambahkan OPD untuk mengelompokkan pengguna dan arsip kontrak.' }}</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $opds->withQueryString()->links() }}</div>
    <dialog id="opd-dialog" class="ui-dialog" aria-labelledby="opd-dialog-title" @if($reopen) data-auto-open @endif>
        <div class="ui-dialog-header"><div><h2 id="opd-dialog-title" data-dialog-title>{{ $record ? 'Ubah OPD' : 'Tambah OPD' }}</h2><p>Kolom bertanda * wajib diisi.</p></div><button type="button" class="ui-close" data-dialog-close aria-label="Tutup form OPD">&times;</button></div>
        <form method="POST" action="{{ $record ? route('admin.opds.update', $record) : route('admin.opds.store') }}">
            @csrf
            <input type="hidden" name="_method" value="PUT" @disabled(!$record)>
            <input type="hidden" name="_modal" value="opd">
            <input type="hidden" name="_record_id" value="{{ old('_record_id') }}">
            @include('admin.opds.partials.fields', ['record' => $record])
            <div class="dialog-actions"><button type="button" class="ui-button btn-secondary" data-dialog-close>Batal</button><button type="submit" class="ui-button btn-primary">Simpan OPD</button></div>
        </form>
    </dialog>
</x-app-layout>
