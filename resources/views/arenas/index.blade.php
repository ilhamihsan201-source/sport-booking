@extends('layouts.app')

@section('title', 'Daftar Arena & Lapangan - SportBooking')

@section('content')

{{-- NAVBAR --}}
<header class="fixed top-0 z-50 w-full border-b border-slate-800/60 bg-[#0B111E]/95 backdrop-blur-md">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center">
            <span class="text-2xl font-black tracking-tight text-white">
                SPORT<span class="text-[#CCFF00]">BOOKING</span>
            </span>
        </a>

        {{-- Navigation Links --}}
        <nav class="hidden items-center gap-8 text-sm font-medium text-slate-300 lg:flex">
            <a href="{{ route('arena.index') }}" class="text-[#CCFF00] font-semibold transition">Arena</a>
            <a href="{{ route('booking.index') }}" class="hover:text-white transition">Jadwal</a>
            <a href="{{ route('home') }}#fasilitas" class="hover:text-white transition">Fasilitas</a>
            <a href="{{ route('home') }}#cara-pesan" class="hover:text-white transition">Cara Pesan</a>
        </nav>

        <div class="flex items-center gap-4">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="rounded-md border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                        Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('customer.dashboard') }}"
                       class="rounded-md border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                        Dashboard Saya
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="text-xs font-medium text-slate-400 hover:text-white transition px-2 py-1">
                    Masuk
                </a>
            @endauth

            <a href="{{ route('booking.index') }}"
               class="rounded-md bg-[#CCFF00] px-4 py-2 text-xs font-bold text-slate-950 transition hover:bg-[#b8e600] active:scale-95 shadow-xs">
                Pesan Sekarang
            </a>
        </div>
    </div>
</header>

<main class="min-h-screen bg-[#f8fafc] pt-24 pb-20">

    {{-- HERO / HEADER --}}
    <section class="bg-[#090d16] py-14 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-[#ccff00]/25 bg-[#ccff00]/10 px-3.5 py-1 text-xs font-semibold text-[#ccff00]">
                    Katalog Arena
                </span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">
                    Pilihan Arena & Lapangan
                </h1>
                <p class="mt-2 text-sm text-slate-300">
                    Temukan lapangan Padel dan Mini Soccer berkualitas tinggi dengan fasilitas lengkap dan standar turnamen.
                </p>
            </div>
        </div>
    </section>

    {{-- FILTER & LISTING --}}
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Filter Tabs --}}
        @if (isset($sports) && count($sports) > 0)
            <div class="mb-8 flex items-center gap-3 overflow-x-auto pb-2">
                <a href="{{ route('arena.index') }}"
                   class="shrink-0 rounded-full px-5 py-2.5 text-xs font-bold transition {{ !request('sport') ? 'bg-[#090d16] text-white shadow' : 'border border-slate-200 bg-white text-slate-600 hover:border-[#ccff00]' }}">
                    Semua Lapangan
                </a>
                @foreach ($sports as $sport)
                    <a href="{{ route('arena.show', $sport->slug) }}"
                       class="shrink-0 rounded-full border px-5 py-2.5 text-xs font-semibold transition {{ request('sport') === $sport->slug ? 'border-[#ccff00] bg-[#ccff00] text-[#090d16]' : 'border-slate-200 bg-white text-slate-600 hover:border-[#ccff00]' }}">
                        {{ $sport->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Court Cards Grid --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($courts ?? [] as $court)
                @php
                    $isSoccer = ($court->sport?->slug === 'mini-soccer') || str_contains(strtolower($court->name ?? ''), 'soccer') || str_contains(strtolower($court->name ?? ''), 'futsal');
                    $fallbackImage = $isSoccer
                        ? 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=1200&auto=format&fit=crop&q=80'
                        : 'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=1200&auto=format&fit=crop&q=80';

                    $primaryImage = $court->images?->firstWhere('is_primary', true) ?? $court->images?->first();
                    $imageUrl = $primaryImage
                        ? asset('storage/' . $primaryImage->image_path)
                        : $fallbackImage;
                @endphp

                <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $primaryImage?->alt_text ?? $court->name }}"
                            onerror="this.onerror=null; this.src='{{ $fallbackImage }}';"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        >

                        <span class="absolute left-4 top-4 rounded-full bg-[#ccff00] px-3 py-1 text-xs font-bold text-slate-950">
                            {{ $court->sport->name ?? 'Olahraga' }}
                        </span>

                        @if ($court->is_featured ?? false)
                            <span class="absolute right-4 top-4 rounded-full bg-slate-950/80 px-3 py-1 text-xs font-semibold text-white">
                                Pilihan Unggulan
                            </span>
                        @endif
                    </div>

                    <div class="p-5">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-blue-700">
                            {{ $court->venue->name ?? 'SportBooking Arena' }}
                        </p>

                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-[#0a369d] transition">
                            <a href="{{ route('courts.show', $court->slug ?? $court->id) }}">
                                {{ $court->name }}
                            </a>
                        </h3>

                        <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-slate-500">
                            {{ $court->description }}
                        </p>

                        @if (isset($court->facilities) && count($court->facilities) > 0)
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @foreach ($court->facilities->take(3) as $facility)
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                        {{ $facility->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-[10px] text-slate-400">Harga mulai</p>
                                <p class="text-base font-extrabold text-[#0a369d]">
                                    Rp{{ number_format((float) ($court->price_per_hour ?? 0), 0, ',', '.') }}
                                    <span class="text-[11px] font-normal text-slate-500">/ jam</span>
                                </p>
                            </div>

                            <a href="{{ route('courts.show', $court->slug ?? $court->id) }}"
                               class="rounded-xl bg-[#ccff00] px-4 py-2 text-xs font-bold text-[#090d16] transition hover:bg-[#b8e600]">
                                Detail
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 p-12 text-center bg-white">
                    <span class="material-symbols-outlined text-4xl text-slate-300">sports_tennis</span>
                    <h3 class="mt-3 text-base font-bold text-slate-900">
                        Belum ada lapangan yang tersedia
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Silakan kembali lagi nanti atau hubungi pengelola arena.
                    </p>
                </div>
            @endforelse
        </div>

    </div>

</main>

{{-- FOOTER --}}
<footer class="border-t border-white/10 bg-[#090d16] px-4 py-8 text-white sm:px-6 lg:px-8">
    <div class="mx-auto flex max-w-7xl flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
        <p>© {{ date('Y') }} SportBooking Indonesia. All rights reserved.</p>
        <a href="{{ route('home') }}" class="hover:text-[#ccff00]">Kembali ke Beranda</a>
    </div>
</footer>

@endsection
