@extends('layouts.app')

@section('title', 'Masuk ke Akun - SportBooking')

@section('content')
<div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-[#090D16] px-4 py-12 sm:px-6 lg:px-8">

    {{-- Background Glow --}}
    <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-[#CCFF00]/10 blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-blue-600/10 blur-3xl"></div>

    {{-- Brand Logo Header --}}
    <div class="relative z-10 mb-8 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 group">
            <span class="text-3xl font-black tracking-tight text-white">
                SPORT<span class="text-[#CCFF00]">BOOKING</span>
            </span>
        </a>
        <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            Akses Layanan Pemesanan Lapangan Padel & Mini Soccer
        </p>
    </div>

    {{-- Main Login Card --}}
    <div class="relative z-10 w-full max-w-md rounded-3xl border border-slate-800/80 bg-[#0F172A]/90 p-8 shadow-2xl backdrop-blur-xl sm:p-10">

        <div class="mb-6">
            <h2 class="text-2xl font-black tracking-tight text-white">
                Masuk ke Akun
            </h2>
            <p class="mt-1 text-xs text-slate-400">
                Silakan masukkan email dan password untuk melanjutkan.
            </p>
        </div>

        {{-- Session Status Alert --}}
        @if (session('status'))
            <div class="mb-5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3.5 text-xs font-semibold text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        {{-- General Error Alert --}}
        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-500/30 bg-red-500/10 p-3.5 text-xs text-red-400">
                <span class="font-bold">Gagal Masuk:</span>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email Input --}}
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Alamat Email <span class="text-[#CCFF00]">*</span>
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                    class="w-full rounded-xl border border-slate-700 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder-slate-500 outline-none transition focus:border-[#CCFF00] focus:ring-2 focus:ring-[#CCFF00]/20 @error('email') border-red-500 @enderror"
                >
                @error('email')
                    <p class="mt-1.5 text-xs font-semibold text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Input with Show/Hide Toggle --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Kata Sandi <span class="text-[#CCFF00]">*</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-slate-400 hover:text-[#CCFF00] transition">
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>

                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-slate-700 bg-slate-900/80 px-4 py-3 pr-12 text-sm text-white placeholder-slate-500 outline-none transition focus:border-[#CCFF00] focus:ring-2 focus:ring-[#CCFF00]/20 @error('password') border-red-500 @enderror"
                    >
                    <button
                        type="button"
                        id="toggle-password"
                        onclick="togglePasswordVisibility('password', 'eye-icon')"
                        aria-label="Tampilkan atau sembunyikan kata sandi"
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-white transition focus:outline-none"
                    >
                        <span id="eye-icon" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs font-semibold text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="flex items-center gap-2.5 cursor-pointer">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-blue-600 focus:ring-0 focus:ring-offset-0"
                    >
                    <span class="text-xs font-medium text-slate-300">Ingat saya di perangkat ini</span>
                </label>
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#CCFF00] py-3.5 text-xs font-black uppercase tracking-wider text-slate-950 shadow-lg shadow-[#CCFF00]/20 transition hover:bg-[#b8e600] active:scale-[0.98] cursor-pointer"
                >
                    Masuk Sekarang
                </button>
            </div>
        </form>

        {{-- Footer Link to Register --}}
        <div class="mt-6 border-t border-slate-800/80 pt-5 text-center text-xs text-slate-400">
            Belum memiliki akun SportBooking?
            <a href="{{ route('register') }}" class="font-bold text-[#CCFF00] hover:underline ml-1">
                Daftar Akun Baru
            </a>
        </div>

    </div>

    {{-- Back to Home --}}
    <div class="relative z-10 mt-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Kembali ke Halaman Utama</span>
        </a>
    </div>

</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endsection
