<x-guest-layout>
    <x-slot name="title">Masuk</x-slot>
    <div class="login-heading"><p class="login-eyebrow">RUANG ARSIP KONTRAK</p><h1>Masuk ke ARKONDA</h1><p class="text-sm text-slate-500 mt-2">Akses arsip kontrak sesuai kewenangan OPD Anda.</p></div>
    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf
        <div><label for="email" class="login-label">Email</label><input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="ui-input w-full" placeholder="nama@instansi.go.id" @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>@error('email')<p id="email-error" class="text-xs text-red-600 mt-1.5">{{ $message }}</p>@enderror</div>
        <div x-data="{ visible: false }"><label for="password" class="login-label">Kata sandi</label><div class="flex gap-2"><input type="password" :type="visible ? 'text' : 'password'" id="password" name="password" autocomplete="current-password" required class="ui-input w-full flex-1"><button type="button" @click="visible = !visible" :aria-pressed="visible" class="ui-button btn-secondary" aria-controls="password"><span x-text="visible ? 'Sembunyikan' : 'Lihat'">Lihat</span><span class="sr-only"> kata sandi</span></button></div>@error('password')<p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>@enderror</div>
        <label class="login-remember flex items-center gap-2"><input type="checkbox" name="remember" @checked(old('remember')) class="h-4 w-4 accent-blue-600">Ingat saya selama 14 hari</label>
        <button type="submit" class="ui-button btn-primary login-submit w-full"><span>Masuk ke dashboard</span><span aria-hidden="true">→</span></button>
    </form>
    <p class="login-register">Belum memiliki akun? <a href="{{ route('register') }}" class="text-blue-600 font-semibold">Daftar akun</a></p>
</x-guest-layout>
