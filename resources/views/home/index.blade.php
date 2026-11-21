@extends('layouts.app')

@section('title', 'SportBooking - Temukan Lapangan Padel & Mini Soccer Terbaik')

@section('content')

{{-- NAVBAR --}}
<header class="fixed top-0 z-50 w-full border-b border-slate-800/60 bg-[#0B111E]/95 backdrop-blur-md">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">

        {{-- Brand Logo --}}
        <a href="{{ route('home') }}" class="flex items-center">
            <span class="text-2xl font-black tracking-tight text-white">
                SPORT<span class="text-[#CCFF00]">BOOKING</span>
            </span>
        </a>

        {{-- Navigation Links (Title Case, Medium Weight) --}}
        <nav class="hidden items-center gap-8 text-sm font-medium text-slate-300 lg:flex">
            <a href="{{ route('arena.index') }}" class="hover:text-white transition">Arena</a>
            <a href="{{ route('booking.index') }}" class="hover:text-white transition">Jadwal</a>
            <a href="#fasilitas" class="hover:text-white transition">Fasilitas</a>
            <a href="#cara-pesan" class="hover:text-white transition">Cara Pesan</a>
        </nav>

        {{-- Actions --}}
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

<main>

    {{-- HERO SECTION --}}
    <section class="relative flex min-h-[720px] items-center overflow-hidden bg-[#090d16] pt-28 pb-20">

        {{-- Background Imagery & Dark Overlay --}}
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=1920&auto=format&fit=crop&q=80"
                alt="Arena Padel Indoor SportBooking"
                class="h-full w-full object-cover object-center opacity-40 brightness-75"
            >
            <div class="absolute inset-0 bg-[#090d16]/70"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#090d16] via-transparent to-transparent"></div>
        </div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl">

                {{-- Status Pill (Clean without gaudy AI lightning icon) --}}
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3.5 py-1.5 text-xs font-medium text-slate-300 backdrop-blur-md">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span>Pemesanan Lapangan Real-Time</span>
                </div>

                {{-- Main Headline --}}
                <h1 class="text-4xl font-black uppercase tracking-tight text-white sm:text-6xl md:text-7xl leading-[1.08]">
                    PESAN LAPANGAN<br>
                    <span class="text-[#CCFF00]">DALAM HITUNGAN DETIK.</span>
                </h1>

                {{-- Subtitle --}}
                <p class="mt-6 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                    Rasakan pengalaman olahraga terbaik. Temukan lapangan Padel dan Mini Soccer, pilih jadwal bermain, dan lakukan pemesanan dengan mudah melalui SportBooking.
                </p>

                {{-- Floating Quick Search / Filter Bar --}}
                <div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-xl shadow-2xl sm:p-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-4 sm:gap-2">
                        {{-- Sport Type --}}
                        <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-left">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Olahraga</label>
                            <select class="mt-0.5 w-full bg-transparent text-xs font-medium text-white focus:outline-none">
                                <option value="padel" class="bg-[#0f172a] text-white">🎾 Padel Court</option>
                                <option value="minisoccer" class="bg-[#0f172a] text-white">⚽ Mini Soccer</option>
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-left">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Tanggal</label>
                            <input type="date" value="{{ date('Y-m-d') }}" class="mt-0.5 w-full bg-transparent text-xs font-medium text-white focus:outline-none [color-scheme:dark]">
                        </div>

                        {{-- Time Slot --}}
                        <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-left">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Waktu Main</label>
                            <select class="mt-0.5 w-full bg-transparent text-xs font-medium text-white focus:outline-none">
                                <option class="bg-[#0f172a] text-white">Pagi (06:00 - 11:00)</option>
                                <option class="bg-[#0f172a] text-white">Siang (11:00 - 15:00)</option>
                                <option class="bg-[#0f172a] text-white" selected>Malam (16:00 - 23:00)</option>
                            </select>
                        </div>

                        {{-- Submit Button --}}
                        <a href="{{ route('booking.index') }}" class="flex items-center justify-center gap-2 rounded-lg bg-[#ccff00] px-4 py-3 text-xs font-bold text-[#090d16] transition hover:bg-[#b8e600]">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                            <span>Cari Slot</span>
                        </a>
                    </div>
                </div>

                {{-- Hero Highlights / Stats (Clean white typography, no neon overload) --}}
                <div class="mt-12 grid grid-cols-2 gap-6 border-t border-white/10 pt-8 sm:grid-cols-4">

                    <div>
                        <p class="text-3xl font-extrabold text-white tracking-tight">
                            {{ $stats['courts'] }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-slate-400">
                            Lapangan Aktif
                        </p>
                    </div>

                    <div>
                        <p class="text-3xl font-extrabold text-white tracking-tight">
                            {{ $stats['venues'] }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-slate-400">
                            Lokasi Arena
                        </p>
                    </div>

                    <div>
                        <p class="text-3xl font-extrabold text-white tracking-tight">
                            {{ $stats['sports'] }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-slate-400">
                            Jenis Olahraga
                        </p>
                    </div>

                    <div>
                        <p class="text-2xl font-extrabold text-white tracking-tight">
                            @if ($todayHours && !$todayHours->is_closed)
                                {{ substr($todayHours->opens_at, 0, 5) }} – {{ substr($todayHours->closes_at, 0, 5) }}
                            @else
                                Tutup
                            @endif
                        </p>
                        <p class="mt-1 text-xs font-medium text-slate-400">
                            Jam Operasional
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </section>

    {{-- ARENA SHOWCASE SECTION --}}
    <section id="arena" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-[#0a369d]">
                    <span class="h-2 w-2 rounded-full bg-[#0a369d]"></span>
                    Pilihan Lapangan
                </span>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Temukan Arena Favoritmu
                </h2>
            </div>
            <p class="max-w-md text-xs leading-relaxed text-slate-500 sm:text-sm">
                Setiap lapangan dilengkapi pencahayaan standar turnamen, rumput grade A, dan kaca panoramik tanpa pilar tengah.
            </p>
        </div>

        {{-- Filter Pills --}}
        <div class="mt-8 flex items-center gap-3 overflow-x-auto pb-3">
            <a
                href="{{ route('home') }}"
                class="shrink-0 rounded-full px-5 py-3 text-sm font-bold transition
                {{ !$selectedSport
                    ? 'bg-slate-950 text-white shadow-lg'
                    : 'border border-slate-200 bg-white text-slate-600 hover:border-[#CCFF00] hover:text-slate-950' }}"
            >
                Semua Lapangan ({{ $stats['courts'] }})
            </a>

            @foreach ($sports as $sport)
                <a
                    href="{{ route('home', ['sport' => $sport->slug]) }}"
                    class="shrink-0 rounded-full border px-5 py-3 text-sm font-semibold transition
                    {{ $selectedSport?->id === $sport->id
                        ? 'border-[#CCFF00] bg-[#CCFF00] text-slate-950 shadow-md'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-[#CCFF00] hover:text-slate-950' }}"
                >
                    @if ($sport->slug === 'padel')
                        🎾
                    @elseif ($sport->slug === 'mini-soccer')
                        ⚽
                    @endif

                    {{ $sport->name }} ({{ $sport->courts_count }})
                </a>
            @endforeach
        </div>

        {{-- Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($courts as $court)
                @php
                    $isSoccer = ($court->sport?->slug === 'mini-soccer') || str_contains(strtolower($court->name ?? ''), 'soccer') || str_contains(strtolower($court->name ?? ''), 'futsal');
                    $fallbackImage = $isSoccer
                        ? 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=1200&auto=format&fit=crop&q=80'
                        : 'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=1200&auto=format&fit=crop&q=80';

                    $primaryImage = $court->images->firstWhere('is_primary', true)
                        ?? $court->images->first();

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

                        <span class="absolute left-4 top-4 rounded-full bg-[#CCFF00] px-3 py-1 text-xs font-bold text-slate-950">
                            {{ $court->sport->name }}
                        </span>

                        @if ($court->is_featured)
                            <span class="absolute right-4 top-4 rounded-full bg-slate-950/80 px-3 py-1 text-xs font-semibold text-white">
                                Pilihan Unggulan
                            </span>
                        @endif
                    </div>

                    <div class="p-5">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-blue-700">
                            {{ $court->venue->name }}
                        </p>

                        <h3 class="text-xl font-extrabold text-slate-900 group-hover:text-blue-700 transition">
                            <a href="{{ route('courts.show', $court->slug ?? $court->id) }}">
                                {{ $court->name }}
                            </a>
                        </h3>

                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500">
                            {{ $court->description }}
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($court->facilities->take(3) as $facility)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                    {{ $facility->name }}
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-xs text-slate-500">Harga mulai</p>
                                <p class="text-lg font-extrabold text-slate-950">
                                    Rp{{ number_format((float) $court->price_per_hour, 0, ',', '.') }}
                                    <span class="text-xs font-medium text-slate-500">/ jam</span>
                                </p>
                            </div>

                            <a
                                href="{{ route('courts.show', $court->slug ?? $court->id) }}"
                                class="rounded-xl bg-[#CCFF00] px-4 py-3 text-sm font-bold text-slate-950 transition hover:bg-lime-400"
                            >
                                Detail & Pesan
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                    <h3 class="text-lg font-bold text-slate-900">
                        Belum ada lapangan tersedia
                    </h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Silakan periksa kembali nanti.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- SCHEDULE PREVIEW SECTION --}}
    <section id="jadwal" class="border-y border-slate-800/60 bg-[#0B111E] px-4 py-16 sm:py-20 text-white sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">

            <div class="text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-800/60 px-3.5 py-1 text-xs font-medium text-slate-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span>Ketersediaan Jadwal Lapangan</span>
                </div>
                <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                    Pilih Jam & Mulai Bermain
                </h2>
                <p class="mx-auto mt-2 max-w-lg text-xs leading-relaxed text-slate-400 sm:text-sm">
                    Ketersediaan diperbarui secara real-time. Klik slot yang tersedia untuk langsung mengamankan lapanganmu.
                </p>
            </div>

            {{-- Date Selector Tabs --}}
            <div class="mt-8 flex justify-center gap-2 overflow-x-auto pb-1 text-xs font-medium">
                <a href="{{ route('booking.index') }}"
                   class="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 font-bold text-white shadow-xs hover:border-slate-600 transition">
                    Hari Ini ({{ date('d M') }})
                </a>
                <a href="{{ route('booking.index') }}"
                   class="rounded-lg border border-transparent px-4 py-2 text-slate-400 hover:border-slate-800 hover:bg-slate-800/50 hover:text-white transition">
                    Besok ({{ date('d M', strtotime('+1 day')) }})
                </a>
                <a href="{{ route('booking.index') }}"
                   class="rounded-lg border border-transparent px-4 py-2 text-slate-400 hover:border-slate-800 hover:bg-slate-800/50 hover:text-white transition hidden sm:inline-block">
                    {{ date('D, d M', strtotime('+2 day')) }}
                </a>
                <a href="{{ route('booking.index') }}"
                   class="rounded-lg border border-transparent px-4 py-2 text-slate-400 hover:border-slate-800 hover:bg-slate-800/50 hover:text-white transition hidden sm:inline-block">
                    {{ date('D, d M', strtotime('+3 day')) }}
                </a>
            </div>

            {{-- Interactive Slots Card --}}
            <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-900/70 p-5 sm:p-6 shadow-xl">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="text-sm font-bold text-white">
                            {{ $courts->first()?->name ?? 'Padel Panoramic Court' }}
                        </span>
                        <span class="rounded-md bg-blue-500/10 border border-blue-500/20 px-2 py-0.5 text-[10px] font-semibold text-blue-400">
                            {{ $courts->first()?->sport->name ?? 'Padel' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            Tersedia
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-slate-600"></span>
                            Terisi
                        </span>
                    </div>
                </div>

                {{-- Time Slots Grid (Every slot links directly to real scheduler) --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-6">
                    {{-- Slot 1: Terisi --}}
                    <div class="rounded-xl border border-slate-800/80 bg-slate-800/30 p-3 text-center opacity-50 cursor-not-allowed">
                        <p class="text-xs font-semibold text-slate-400 line-through">07:00 - 08:00</p>
                        <p class="mt-1 text-[10px] font-medium text-slate-500">Terisi</p>
                    </div>

                    {{-- Slot 2: Terisi --}}
                    <div class="rounded-xl border border-slate-800/80 bg-slate-800/30 p-3 text-center opacity-50 cursor-not-allowed">
                        <p class="text-xs font-semibold text-slate-400 line-through">08:00 - 09:00</p>
                        <p class="mt-1 text-[10px] font-medium text-slate-500">Terisi</p>
                    </div>

                    {{-- Slot 3: Tersedia --}}
                    <a href="{{ route('booking.index') }}"
                       class="group rounded-xl border border-slate-700 bg-slate-800/60 p-3 text-center transition hover:border-[#CCFF00] hover:bg-slate-800 hover:-translate-y-0.5 shadow-xs">
                        <p class="text-xs font-bold text-white group-hover:text-[#CCFF00] transition">09:00 - 10:00</p>
                        <p class="mt-1 text-[10px] font-semibold text-emerald-400">Pesan Slot</p>
                    </a>

                    {{-- Slot 4: Tersedia --}}
                    <a href="{{ route('booking.index') }}"
                       class="group rounded-xl border border-slate-700 bg-slate-800/60 p-3 text-center transition hover:border-[#CCFF00] hover:bg-slate-800 hover:-translate-y-0.5 shadow-xs">
                        <p class="text-xs font-bold text-white group-hover:text-[#CCFF00] transition">10:00 - 11:00</p>
                        <p class="mt-1 text-[10px] font-semibold text-emerald-400">Pesan Slot</p>
                    </a>

                    {{-- Slot 5: Tersedia --}}
                    <a href="{{ route('booking.index') }}"
                       class="group rounded-xl border border-slate-700 bg-slate-800/60 p-3 text-center transition hover:border-[#CCFF00] hover:bg-slate-800 hover:-translate-y-0.5 shadow-xs">
                        <p class="text-xs font-bold text-white group-hover:text-[#CCFF00] transition">16:00 - 17:00</p>
                        <p class="mt-1 text-[10px] font-semibold text-emerald-400">Pesan Slot</p>
                    </a>

                    {{-- Slot 6: Tersedia (Prime Time) --}}
                    <a href="{{ route('booking.index') }}"
                       class="group rounded-xl border border-slate-700 bg-slate-800/60 p-3 text-center transition hover:border-[#CCFF00] hover:bg-slate-800 hover:-translate-y-0.5 shadow-xs">
                        <p class="text-xs font-bold text-white group-hover:text-[#CCFF00] transition">19:00 - 20:00</p>
                        <p class="mt-1 text-[10px] font-semibold text-emerald-400">Prime Time</p>
                    </a>
                </div>

                {{-- Action & Customer Support Bar --}}
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-xl border border-slate-800 bg-slate-800/40 p-4">
                    <div class="text-center sm:text-left">
                        <p class="text-xs font-semibold text-slate-200">
                            Ingin sewa rutin mingguan atau reservasi event komunitas?
                        </p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Customer service kami siap membantu penjadwalan khusus.
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20SportBooking,%20mau%20tanya%20sewa%20rutin" target="_blank"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-700 hover:text-white">
                            <span class="material-symbols-outlined text-[16px] text-emerald-400">chat</span>
                            <span>WhatsApp</span>
                        </a>

                        <a href="{{ route('booking.index') }}"
                           class="inline-flex items-center gap-1 rounded-lg bg-[#CCFF00] px-4 py-2 text-xs font-bold text-slate-950 transition hover:bg-[#b8e600] active:scale-95 shadow-xs">
                            <span>Buka Jadwal Lengkap</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- FASILITAS ARENA --}}
    <section id="fasilitas" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-[#0a369d]">
                <span class="h-2 w-2 rounded-full bg-[#0a369d]"></span>
                Fasilitas Lengkap
            </span>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Kenyamanan Ekstra Setelah Bermain
            </h2>
            <p class="mx-auto mt-2 max-w-lg text-xs text-slate-500 sm:text-sm">
                Dirancang khusus untuk mendukung performa dan relaksasi para pemain.
            </p>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['stadium', 'Lampu LED Turnamen', 'Penerangan 500+ lux tanpa bayangan menyilaukan untuk sesi malam hari.'],
                ['shower', 'Shower & Water Heater', 'Kamar bilas bersih dengan air panas dan sabun wangi gratis.'],
                ['sports_tennis', 'Penyewaan Raket & Bola', 'Tersedia raket padel Babolat/Bullpadel dan bola resmi turnamen.'],
                ['coffee', 'Sport Lounge & Cafe', 'Area istirahat ber-AC dengan sajian minuman ion, jus segar, dan kopi.'],
                ['lock', 'Loker Kunci Digital', 'Simpan tas, sepatu, dan barang berharga dengan aman selama bertanding.'],
                ['wifi', 'High-Speed Wi-Fi', 'Koneksi internet cepat untuk live streaming atau kerja santai dari cafe.'],
                ['local_parking', 'Parkir Luas & Aman', 'Area parkir mobil dan motor kapasitas 50+ kendaraan dengan security 24 jam.'],
                ['medical_services', 'P3K & Ice Compression', 'Penanganan pertama siap siaga untuk cedera ringan atau kram otot.']
            ] as [$icon, $title, $description])
                <div class="group rounded-2xl border border-slate-200/80 bg-white p-5 transition hover:border-[#ccff00] hover:shadow-lg">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#0a369d] transition group-hover:bg-[#ccff00] group-hover:text-[#090d16]">
                        <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
                    </span>
                    <h3 class="mt-4 text-sm font-bold text-slate-900">{{ $title }}</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-500">{{ $description }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CARA PESAN --}}
    <section id="cara-pesan" class="border-y border-slate-200/80 bg-slate-100/60 px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl text-center">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-[#0a369d]">
                <span class="h-2 w-2 rounded-full bg-[#0a369d]"></span>
                Alur Booking
            </span>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Booking Mudah Cuma 3 Langkah
            </h2>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                <div class="relative rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#090d16] text-xs font-extrabold text-[#ccff00]">
                        01
                    </div>
                    <h3 class="mt-4 text-sm font-bold text-slate-900">Pilih Arena & Jadwal</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Tentukan jenis lapangan (Padel / Mini Soccer), pilih tanggal main, dan klik jam yang tersedia.
                    </p>
                </div>

                <div class="relative rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#090d16] text-xs font-extrabold text-[#ccff00]">
                        02
                    </div>
                    <h3 class="mt-4 text-sm font-bold text-slate-900">Pembayaran Instan (QRIS / Transfer)</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Scan QRIS atau transfer bank. Sistem otomatis memvalidasi pembayaran dalam hitungan detik.
                    </p>
                </div>

                <div class="relative rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#090d16] text-xs font-extrabold text-[#ccff00]">
                        03
                    </div>
                    <h3 class="mt-4 text-sm font-bold text-slate-900">Dapatkan Kode Tiket & Main!</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Tiket masuk dan QR code langsung dikirim ke WhatsApp / Email Anda. Cukup tunjukkan ke resepsionis.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- TESTIMONI / SOCIAL PROOF --}}
    <section id="testimoni" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-[#0a369d]">
                <span class="h-2 w-2 rounded-full bg-[#0a369d]"></span>
                Kata Mereka
            </span>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Pengalaman Para Pemain
            </h2>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-1 text-amber-400 text-xs">
                    ★★★★★
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-600">
                    "Lapangannya super nyaman! Kaca padel beneran jernih dan pantulan bolanya konsisten. Bookingnya juga gampang banget tanpa ribet nunggu balasan WhatsApp."
                </p>
                <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-3">
                    <div class="h-8 w-8 rounded-full bg-slate-200 text-xs font-bold flex items-center justify-center text-slate-700">RA</div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Reza Aditya</p>
                        <p class="text-[10px] text-slate-400">Komunitas Padel Jakarta</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-1 text-amber-400 text-xs">
                    ★★★★★
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-600">
                    "Mini soccernya mantap, rumput empuk nggak bikin dengkul sakit. Lampunya terang banget pas main malam. Tempat parkirnya juga luas."
                </p>
                <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-3">
                    <div class="h-8 w-8 rounded-full bg-slate-200 text-xs font-bold flex items-center justify-center text-slate-700">DP</div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Dimas Pratama</p>
                        <p class="text-[10px] text-slate-400">Kapten Tim FC Galaksi</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-1 text-amber-400 text-xs">
                    ★★★★★
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-600">
                    "Fasilitas kamar mandi dan cafenya bintang 5. Selesai tanding bisa nongkrong ngopi dulu sama temen-temen kantor. Recommended!"
                </p>
                <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-3">
                    <div class="h-8 w-8 rounded-full bg-slate-200 text-xs font-bold flex items-center justify-center text-slate-700">SN</div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Sarah Nurhaliza</p>
                        <p class="text-[10px] text-slate-400">Pemain Rutin Weekend</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA BANNER --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#090d16] via-[#0f172a] to-[#0a369d] px-6 py-12 text-center text-white shadow-2xl sm:px-12 sm:py-16">
            <div class="relative z-10 mx-auto max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#ccff00]/10 px-3 py-1 text-[11px] font-bold text-[#ccff00]">
                    Promo Pengguna Baru
                </span>
                <h2 class="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">
                    Siap Berolahraga Hari Ini?
                </h2>
                <p class="mt-2 text-xs text-slate-300 sm:text-sm">
                    Daftar akun sekarang dan dapatkan potongan diskon 10% untuk pemesanan sesi pertamamu.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="#arena" class="rounded-xl bg-[#ccff00] px-5 py-2.5 text-xs font-bold text-[#090d16] transition hover:bg-[#b8e600] hover:scale-105">
                        Pesan Sekarang
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="rounded-xl border border-white/20 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-white/10">
                        Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

{{-- FOOTER --}}
<footer class="border-t border-white/10 bg-[#090d16] px-4 py-12 text-white sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-4">

        {{-- Col 1: Brand --}}
        <div class="md:col-span-1">
            <a href="/" class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#ccff00] text-xs font-black text-[#090d16]">
                    <span class="material-symbols-outlined text-[18px]">sports_tennis</span>
                </span>
                <span class="text-base font-bold tracking-tight text-white">
                    SPORT<span class="text-[#ccff00]">BOOKING</span>
                </span>
            </a>
            <p class="mt-3 text-xs leading-relaxed text-slate-400">
                Solusi pemesanan lapangan olahraga terintegrasi. Cepat, transparan, dan nyaman.
            </p>
        </div>

        {{-- Col 2: Navigation --}}
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-white">Navigasi</p>
            <ul class="mt-3 space-y-2 text-xs text-slate-400">
                <li><a href="#arena" class="hover:text-[#ccff00]">Pilihan Arena</a></li>
                <li><a href="#jadwal" class="hover:text-[#ccff00]">Jadwal Slot</a></li>
                <li><a href="#fasilitas" class="hover:text-[#ccff00]">Fasilitas</a></li>
                <li><a href="#cara-pesan" class="hover:text-[#ccff00]">Cara Pesan</a></li>
            </ul>
        </div>

        {{-- Col 3: Operating Hours --}}
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-white">Jam Operasional</p>
            <ul class="mt-3 space-y-2 text-xs text-slate-400">
                <li>Senin - Jumat: 06:00 - 24:00</li>
                <li>Sabtu - Minggu: 06:00 - 24:00</li>
                <li class="pt-1 text-[#ccff00]">Buka setiap hari & hari libur</li>
            </ul>
        </div>

        {{-- Col 4: Location & Contact --}}
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-white">Lokasi & Kontak</p>
            <p class="mt-3 text-xs leading-relaxed text-slate-400">
                Jl. Danau Sunter Barat No. 12, Jakarta Utara
            </p>
            <p class="mt-2 text-xs text-slate-400">
                WhatsApp: +62 812-3456-7890<br>
                Email: halo@sportbooking.com
            </p>
        </div>
    </div>

    <div class="mx-auto mt-10 max-w-7xl border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500">
        <p>© {{ date('Y') }} SportBooking Indonesia. All rights reserved.</p>
        <div class="flex items-center gap-4">
            <a href="#" class="hover:text-slate-300 transition">Kebijakan Privasi</a>
            <a href="#" class="hover:text-slate-300 transition">Syarat & Ketentuan</a>
            <span class="text-slate-700">•</span>
            <a href="{{ route('login') }}" class="hover:text-slate-300 transition">Portal Admin</a>
        </div>
    </div>
</footer>

@endsection
