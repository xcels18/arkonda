<x-guest-layout>
    <x-slot name="title">Menunggu verifikasi</x-slot>
    <span class="inline-flex px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium mb-4">Menunggu verifikasi</span>
    <h1>Akun sedang ditinjau</h1>
    <p class="text-sm text-slate-500 mt-3">Administrator perlu memverifikasi akun Anda sebelum arsip kontrak dapat diakses.</p>
    <dl class="mt-6 space-y-3 border-y border-slate-200 py-4"><div><dt class="text-xs text-slate-500">Nama pengguna</dt><dd class="font-medium">{{ auth()->user()->name }}</dd></div><div><dt class="text-xs text-slate-500">OPD</dt><dd class="font-medium">{{ auth()->user()->opd?->nama ?? 'Belum ditetapkan' }}</dd></div></dl>
    <p class="text-xs text-slate-500 mt-4">Jika akses diperlukan segera, hubungi administrator Inspektorat.</p>
    <a href="{{ route('verification.pending') }}" class="ui-button btn-primary w-full mt-5">Periksa status verifikasi</a>
    <form action="{{ route('logout') }}" method="POST" class="mt-2">@csrf<button type="submit" class="ui-button btn-secondary w-full">Keluar dari akun</button></form>
</x-guest-layout>
