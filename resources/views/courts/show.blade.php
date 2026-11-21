@extends('layouts.app')

@section('title', ($court->name ?? 'Detail Lapangan') . ' - SportBooking')

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

            <a href="{{ route('booking.index', ['court_id' => $court->id]) }}"
               class="rounded-md bg-[#CCFF00] px-4 py-2 text-xs font-bold text-slate-950 transition hover:bg-[#b8e600] active:scale-95 shadow-xs">
                Pesan Sekarang
            </a>
        </div>
    </div>
</header>

<main class="min-h-screen bg-[#f8fafc] pt-24 pb-20">

    {{-- BREADCRUMB --}}
    <div class="border-b border-slate-200/80 bg-white py-4">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">Beranda</a>
                <span>/</span>
                <a href="{{ route('arena.index') }}" class="hover:text-slate-900">Arena</a>
                @if (isset($court->sport))
                    <span>/</span>
                    <a href="{{ route('arena.show', $court->sport->slug) }}" class="hover:text-slate-900">{{ $court->sport->name }}</a>
                @endif
                <span>/</span>
                <span class="font-semibold text-slate-900">{{ $court->name ?? 'Detail Lapangan' }}</span>
            </nav>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            {{-- LEFT: PHOTO GALLERY & SPECS (2 Cols) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Primary Photo --}}
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

                <div class="relative overflow-hidden rounded-3xl bg-slate-100 shadow-md">
                    <img
                        id="main-court-photo"
                        src="{{ $imageUrl }}"
                        alt="{{ $primaryImage?->alt_text ?? $court->name }}"
                        onerror="this.onerror=null; this.src='{{ $fallbackImage }}';"
                        class="h-[360px] sm:h-[440px] w-full object-cover transition duration-300"
                    >
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="rounded-full bg-[#090d16]/80 px-3 py-1 text-xs font-semibold text-white backdrop-blur-md">
                            {{ $court->sport->name ?? 'Olahraga' }}
                        </span>
                        @if ($court->is_featured ?? false)
                            <span class="rounded-full bg-[#ccff00] px-3 py-1 text-xs font-bold text-[#090d16]">
                                Unggulan
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Image Thumbnails (if multiple images) --}}
                @if (isset($court->images) && count($court->images) > 1)
                    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3" role="tablist" aria-label="Galeri Foto Lapangan">
                        @foreach ($court->images as $index => $img)
                            @php
                                $thumbUrl = asset('storage/' . $img->image_path);
                                $isCurrentPrimary = ($primaryImage && $primaryImage->id === $img->id) || (!$primaryImage && $index === 0);
                            @endphp
                            <button
                                type="button"
                                role="tab"
                                aria-selected="{{ $isCurrentPrimary ? 'true' : 'false' }}"
                                aria-label="Tampilkan foto {{ $index + 1 }} dari {{ count($court->images) }}"
                                data-img-url="{{ $thumbUrl }}"
                                data-img-alt="{{ $img->alt_text ?? ($court->name . ' - Foto ' . ($index + 1)) }}"
                                onclick="switchCourtPhoto(this)"
                                onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); switchCourtPhoto(this); }"
                                class="court-thumb relative overflow-hidden rounded-xl border-2 bg-slate-100 h-20 w-full transition cursor-pointer hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-blue-600 {{ $isCurrentPrimary ? 'border-blue-600 ring-2 ring-blue-600/30' : 'border-slate-200 opacity-70 hover:opacity-100' }}"
                            >
                                <img src="{{ $thumbUrl }}" alt="{{ $img->alt_text ?? $court->name }}" class="h-full w-full object-cover pointer-events-none">
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Title & Description Card --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-700">
                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                        <span>{{ $court->venue->name ?? 'SportBooking Venue' }}</span>
                    </div>

                    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">
                        {{ $court->name ?? 'Nama Lapangan' }}
                    </h1>

                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Tentang Lapangan</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">
                            {{ $court->description ?? 'Lapangan berstandar resmi turnamen dengan pencahayaan anti silau, permukaan berkualitas prima, dan sirkulasi udara yang nyaman untuk permainan optimal.' }}
                        </p>
                    </div>

                    {{-- Facilities List --}}
                    @if (isset($court->facilities) && count($court->facilities) > 0)
                        <div class="mt-6 border-t border-slate-100 pt-6">
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Fasilitas Termasuk</h2>
                            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach ($court->facilities as $facility)
                                    <div class="flex items-center gap-2 rounded-xl bg-slate-50 p-2.5 border border-slate-100">
                                        <span class="material-symbols-outlined text-[18px] text-[#0a369d]">check_circle</span>
                                        <span class="text-xs font-medium text-slate-700">{{ $facility->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Rules / Peraturan --}}
                    <div class="mt-6 border-t border-slate-100 pt-6">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Ketentuan & Peraturan</h2>
                        <ul class="mt-3 space-y-2 text-xs text-slate-500">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">info</span>
                                Wajib mengenakan sepatu olahraga yang sesuai (non-marking untuk indoor padel / turf shoes untuk mini soccer).
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                                Harap hadir 10-15 menit sebelum jam jadwal bermain dimulai.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">no_drinks</span>
                                Dilarang membawa makanan berat ke dalam arena lapangan.
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            {{-- RIGHT: STICKY BOOKING CARD (1 Col) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xl">
                    <p class="text-xs font-medium text-slate-400">Tarif Sewa</p>
                    <div class="mt-1 flex items-baseline gap-1">
                        <span class="text-3xl font-black text-[#0a369d]">
                            Rp{{ number_format((float) ($court->price_per_hour ?? 0), 0, ',', '.') }}
                        </span>
                        <span class="text-xs font-medium text-slate-500">/ jam</span>
                    </div>

                    <div class="mt-6 space-y-4 rounded-xl bg-slate-50 p-4 border border-slate-100 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Status Lapangan</span>
                            <span class="font-bold text-emerald-600">● Aktif & Tersedia</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Kategori</span>
                            <span class="font-semibold text-slate-900">{{ $court->sport->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Lokasi</span>
                            <span class="font-semibold text-slate-900">{{ $court->venue->name ?? 'Pusat Arena' }}</span>
                        </div>
                    </div>

                    {{-- CTA Booking --}}
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('booking.index', ['court_id' => $court->id]) }}"
                           class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#ccff00] py-3.5 text-xs font-extrabold text-[#090d16] shadow-lg transition hover:bg-[#b8e600] hover:scale-[1.02]">
                            <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                            <span>Pilih Jam Main</span>
                        </a>

                        <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20mau%20tanya%20lapangan%20{{ urlencode($court->name ?? 'Lapangan') }}"
                           target="_blank"
                           class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">chat</span>
                            <span>Tanya via WhatsApp</span>
                        </a>
                    </div>

                    <p class="mt-4 text-center text-[11px] text-slate-400">
                        ⚡ Konfirmasi instan • Tiket digital via WA
                    </p>
                </div>
            </div>

        </div>
    </div>

</main>

{{-- FOOTER --}}
<footer class="border-t border-white/10 bg-[#090d16] px-4 py-8 text-white sm:px-6 lg:px-8">
    <div class="mx-auto flex max-w-7xl flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
        <p>© {{ date('Y') }} SportBooking Indonesia. All rights reserved.</p>
        <a href="{{ route('arena.index') }}" class="hover:text-[#ccff00]">← Kembali ke Katalog Arena</a>
    </div>
</footer>

@push('scripts')
<script>
function switchCourtPhoto(button) {
    const mainImg = document.getElementById('main-court-photo');
    if (!mainImg || !button) return;

    const newUrl = button.getAttribute('data-img-url');
    const newAlt = button.getAttribute('data-img-alt');

    if (newUrl) {
        mainImg.src = newUrl;
        if (newAlt) mainImg.alt = newAlt;

        document.querySelectorAll('.court-thumb').forEach(thumb => {
            thumb.classList.remove('border-blue-600', 'ring-2', 'ring-blue-600/30');
            thumb.classList.add('border-slate-200', 'opacity-70');
            thumb.setAttribute('aria-selected', 'false');
        });

        button.classList.remove('border-slate-200', 'opacity-70');
        button.classList.add('border-blue-600', 'ring-2', 'ring-blue-600/30');
        button.setAttribute('aria-selected', 'true');
    }
}
</script>
@endpush

@endsection
