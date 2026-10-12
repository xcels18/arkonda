<x-app-layout>
    <x-slot name="title">Ubah OPD</x-slot>
    <div class="ui-form-single"><div class="mb-6"><a class="text-sm text-blue-600" href="{{ route('admin.opds.index') }}">&larr; Kembali ke daftar OPD</a><h1 class="mt-3">Ubah OPD</h1><p class="text-slate-500 mt-1">Kolom bertanda * wajib diisi.</p></div>
        <div class="ui-card p-6"><form action="{{ route('admin.opds.update', $opd) }}" method="POST">@csrf @method('PUT')
            @include('admin.opds.partials.fields', ['record' => $opd])
            <div class="dialog-actions"><a class="ui-button btn-secondary" href="{{ route('admin.opds.index') }}">Batal</a><button type="submit" class="ui-button btn-primary">Simpan OPD</button></div>
        </form></div>
    </div>
</x-app-layout>
