<x-guest-layout>
    <x-slot name="title">Menunggu Verifikasi</x-slot>

    <div class="text-center">
        <div class="mx-auto h-16 w-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h3 class="text-lg font-medium text-gray-900 mb-2">Menunggu Verifikasi</h3>
        
        <p class="text-sm text-gray-600 mb-6">
            Akun Anda sedang dalam proses verifikasi oleh administrator. 
            Silakan tunggu hingga akun Anda diverifikasi untuk dapat mengakses sistem.
        </p>

        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
            <p class="text-sm text-blue-700">
                <strong>Info:</strong> Anda akan mendapatkan akses setelah administrator memverifikasi akun Anda.
            </p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                Logout
            </button>
        </form>
    </div>
</x-guest-layout>

