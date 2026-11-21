@extends('layouts.app')

@section('title', '403 - Akses Dibatasi | SportBooking')

@section('content')
<div class="relative flex min-h-screen flex-col items-center justify-between bg-[#090D16] text-white">

    {{-- TOP NAVBAR --}}
    <header class="w-full border-b border-white/10 bg-[#090D16]/90 px-4 py-4 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center">
                <span class="text-2xl font-black tracking-tight text-white">
                    SPORT<span class="text-[#CCFF00]">BOOKING</span>
                </span>
            </a>
            <a href="{{ route('home') }}" class="text-xs font-bold uppercase tracking-wider text-slate-300 hover:text-[#CCFF00] transition">
                ← Kembali ke Beranda
            </a>
        </div>
    </header>

    {{-- MAIN ERROR CONTENT --}}
    <main class="relative z-10 flex flex-1 flex-col items-center justify-center px-4 py-16 text-center sm:px-6">
        <div class="max-w-xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-400/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-300 mb-6">
                <span>Akses Ditolak</span>
            </div>

            <h1 class="text-7xl font-black tracking-tight text-[#CCFF00] sm:text-8xl md:text-9xl">
                403
            </h1>

            <h2 class="mt-4 text-2xl font-extrabold text-white sm:text-3xl">
                Akses Dibatasi
            </h2>

            <p class="mt-3 text-sm leading-relaxed text-slate-400 sm:text-base">
                Anda tidak memiliki izin untuk mengakses halaman ini. Silakan masuk dengan akun yang sesuai atau kembali ke halaman utama.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#CCFF00] px-6 py-3.5 text-xs font-black uppercase tracking-wider text-slate-950 shadow-lg shadow-[#CCFF00]/20 transition hover:bg-[#b8e600] active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    <span>Kembali ke Beranda</span>
                </a>

                <a href="{{ route('arena.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-white transition hover:bg-white/10">
                    <span>Lihat Katalog Arena</span>
                </a>
            </div>
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="w-full border-t border-white/10 py-6 text-center text-xs text-slate-500">
        <p>© {{ date('Y') }} SportBooking Indonesia. Hak cipta dilindungi.</p>
    </footer>

</div>
@endsection
