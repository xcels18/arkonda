<x-app-layout>
    <x-slot name="title">Ubah pengguna</x-slot>
    <div class="ui-form-single"><div class="mb-6"><a class="text-sm text-blue-600" href="{{ route('admin.users.index') }}">&larr; Kembali ke daftar pengguna</a><h1 class="mt-3">Ubah pengguna</h1><p class="text-slate-500 mt-1">Perbarui identitas dan OPD pengguna.</p></div>
        <div class="ui-card p-6"><form action="{{ route('admin.users.update', $user) }}" method="POST">@csrf @method('PUT')
            @include('admin.users.partials.fields', ['record' => $user])
            <div class="dialog-actions"><a class="ui-button btn-secondary" href="{{ route('admin.users.index') }}">Batal</a><button type="submit" class="ui-button btn-primary">Simpan perubahan</button></div>
        </form></div>
    </div>
</x-app-layout>
