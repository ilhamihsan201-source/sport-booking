@extends('layouts.app')

@section('title', 'Arena ' . ($sport->name ?? 'Olahraga') . ' - SportBooking')

@section('content')

{{-- NAVBAR --}}
<header class="fixed top-0 z-50 w-full border-b border-white/10 bg-[#090d16]/90 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#ccff00] font-black text-[#090d16] shadow-[0_0_20px_rgba(204,255,0,0.35)] transition group-hover:scale-105">
                <span class="material-symbols-outlined text-[20px]">sports_tennis</span>
            </span>
            <span class="text-lg font-bold tracking-tight text-white">
                SPORT<span class="text-[#ccff00]">BOOKING</span>
            </span>
        </a>

        <nav class="hidden items-center gap-8 text-xs font-semibold uppercase tracking-wider text-slate-300 lg:flex">
            <a href="{{ route('home') }}" class="transition hover:text-[#ccff00]">Beranda</a>
            <a href="{{ route('arena.index') }}" class="text-[#ccff00]">Arena</a>
            <a href="{{ route('booking.index') }}" class="transition hover:text-[#ccff00]">Jadwal</a>
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('booking.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-[#ccff00] px-4 py-2 text-xs font-bold text-[#090d16] shadow-[0_0_16px_rgba(204,255,0,0.25)] transition hover:bg-[#b8e600]">
                <span>Pesan Sekarang</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>
</header>

<main class="min-h-screen bg-[#f8fafc] pt-24 pb-20">

    {{-- BREADCRUMB & HERO --}}
    <section class="bg-[#090d16] py-12 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                <span>/</span>
                <a href="{{ route('arena.index') }}" class="hover:text-white">Arena</a>
                <span>/</span>
                <span class="text-[#ccff00] font-semibold">{{ $sport->name ?? 'Detail Arena' }}</span>
            </nav>

            <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                        Arena {{ $sport->name ?? 'Olahraga' }}
                    </h1>
                    <p class="mt-2 text-xs text-slate-300 sm:text-sm max-w-xl">
                        {{ $sport->description ?? 'Pilihan lapangan berstandar turnamen internasional dengan fasilitas modern dan booking online instan.' }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-bold text-[#ccff00]">
                        {{ isset($courts) ? count($courts) : ($sport->courts_count ?? 0) }} Lapangan Tersedia
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- LISTING --}}
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($courts ?? ($sport->courts ?? []) as $court)
                @php
                    $primaryImage = $court->images?->firstWhere('is_primary', true) ?? $court->images?->first();
                    $imageUrl = $primaryImage
                        ? asset('storage/' . $primaryImage->image_path)
                        : 'https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=1000';
                @endphp

                <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $primaryImage?->alt_text ?? $court->name }}"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=1000';"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        >

                        <span class="absolute left-4 top-4 rounded-full bg-[#ccff00] px-3 py-1 text-xs font-bold text-slate-950">
                            {{ $sport->name ?? 'Olahraga' }}
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
                                Pesan
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 p-12 text-center bg-white">
                    <span class="material-symbols-outlined text-4xl text-slate-300">sports_tennis</span>
                    <h3 class="mt-3 text-base font-bold text-slate-900">
                        Belum ada lapangan di kategori ini
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Silakan cek kategori olahraga lainnya.
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
        <a href="{{ route('arena.index') }}" class="hover:text-[#ccff00]">← Kembali ke Semua Arena</a>
    </div>
</footer>

@endsection
