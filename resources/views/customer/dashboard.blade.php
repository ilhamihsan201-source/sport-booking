<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">
                    SportBooking Customer Portal
                </p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                    Dashboard Saya
                </h2>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('booking.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#0B1F4D] px-4 py-2 text-sm font-bold text-[#CCFF00] shadow-sm hover:bg-slate-800 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Pesan Lapangan Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Welcome banner --}}
            <section class="relative overflow-hidden rounded-3xl bg-[#0B1F4D] p-6 text-white shadow-lg sm:p-8">
                <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>
                <div class="absolute -bottom-24 right-1/3 h-48 w-48 rounded-full bg-lime-400/10 blur-3xl"></div>

                <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-200">
                            Selamat datang kembali,
                        </p>
                        <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                            {{ $user->name }}
                            <span class="text-[#CCFF00]">.</span>
                        </h1>
                        <p class="mt-2 max-w-xl text-sm leading-6 text-blue-100">
                            Kelola jadwal latihan, pantau status pembayaran, dan temukan lapangan terbaik Anda.
                        </p>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#CCFF00] text-xl font-black text-[#0B1F4D]">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xs text-blue-200">Akun Member</p>
                            <p class="font-bold">{{ $user->email }}</p>
                            <p class="text-xs text-lime-300">Status: Aktif</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Statistics cards --}}
            <section class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Booking</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['total'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Semua pesanan</p>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Menunggu Bayar</p>
                    <p class="mt-2 text-3xl font-black text-amber-700">{{ $stats['pending'] }}</p>
                    <p class="mt-1 text-xs text-amber-600">Perlu tindakan</p>
                </div>

                <div class="rounded-2xl border border-blue-200 bg-blue-50/50 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Dikonfirmasi</p>
                    <p class="mt-2 text-3xl font-black text-blue-800">{{ $stats['confirmed'] }}</p>
                    <p class="mt-1 text-xs text-blue-600">Jadwal aktif</p>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Selesai Main</p>
                    <p class="mt-2 text-3xl font-black text-emerald-800">{{ $stats['completed'] }}</p>
                    <p class="mt-1 text-xs text-emerald-600">Selesai</p>
                </div>
            </section>

            {{-- Filter tabs & Booking List --}}
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-5">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">
                            Riwayat & Status Booking
                        </h3>
                        <p class="text-xs text-slate-500">Daftar pemesanan lapangan Anda.</p>
                    </div>

                    {{-- Status filters --}}
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('customer.dashboard') }}"
                           class="rounded-xl px-3 py-1.5 text-xs font-bold transition {{ !$status ? 'bg-[#0B1F4D] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua
                        </a>
                        <a href="{{ route('customer.dashboard', ['status' => 'pending_payment']) }}"
                           class="rounded-xl px-3 py-1.5 text-xs font-bold transition {{ $status === 'pending_payment' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Menunggu Bayar
                        </a>
                        <a href="{{ route('customer.dashboard', ['status' => 'confirmed']) }}"
                           class="rounded-xl px-3 py-1.5 text-xs font-bold transition {{ $status === 'confirmed' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Dikonfirmasi
                        </a>
                        <a href="{{ route('customer.dashboard', ['status' => 'completed']) }}"
                           class="rounded-xl px-3 py-1.5 text-xs font-bold transition {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Selesai
                        </a>
                        <a href="{{ route('customer.dashboard', ['status' => 'cancelled']) }}"
                           class="rounded-xl px-3 py-1.5 text-xs font-bold transition {{ $status === 'cancelled' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Dibatalkan
                        </a>
                    </div>
                </div>

                {{-- Bookings list --}}
                <div class="mt-6 space-y-4">
                    @forelse($bookings as $booking)
                        @php
                            $latestPayment = $booking->payments->first();
                        @endphp
                        <div class="rounded-2xl border border-slate-200 p-5 hover:border-blue-300 transition hover:shadow-sm">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-black tracking-wider text-[#CCFF00]">
                                            {{ $booking->booking_code }}
                                        </span>

                                        @if($booking->status === 'pending_payment')
                                            @if($latestPayment && $latestPayment->status === 'pending')
                                                <span class="rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-800">
                                                    Sedang menunggu pemeriksaan admin
                                                </span>
                                            @else
                                                <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">
                                                    Menunggu Pembayaran
                                                </span>
                                            @endif
                                        @elseif($booking->status === 'confirmed')
                                            <span class="rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">
                                                Jadwal Dikonfirmasi
                                            </span>
                                        @elseif($booking->status === 'completed')
                                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                                Selesai Digunakan
                                            </span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="rounded-lg bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">
                                                Dibatalkan
                                            </span>
                                        @elseif($booking->status === 'expired')
                                            <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">
                                                Kedaluwarsa
                                            </span>
                                        @endif

                                        <span class="text-xs text-slate-400">
                                            Dibuat: {{ $booking->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>

                                    <div>
                                        <h4 class="text-lg font-black text-slate-900">
                                            {{ $booking->court->name ?? 'Lapangan' }}
                                        </h4>
                                        <p class="text-xs font-medium text-slate-500">
                                            {{ $booking->court->sport->name ?? 'Olahraga' }} &bull; {{ $booking->court->venue->name ?? 'Venue' }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-700">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14H4V6a1 1 0 011-1z"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y') }}
                                        </div>

                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} ({{ $booking->duration_hours }} Jam)
                                        </div>
                                    </div>

                                    {{-- Info status pembayaran terbaru --}}
                                    @if($latestPayment)
                                        <div class="rounded-xl bg-slate-50 p-2.5 text-xs text-slate-600 flex items-center gap-2">
                                            <span class="font-bold">Status Bukti Transfer:</span>
                                            @if($latestPayment->status === 'verified')
                                                <span class="text-emerald-700 font-bold">Terverifikasi Admin</span>
                                            @elseif($latestPayment->status === 'rejected')
                                                <span class="text-red-700 font-bold">Ditolak: {{ $latestPayment->rejection_reason }}</span>
                                            @else
                                                <span class="text-amber-700 font-bold">Sedang diverifikasi oleh admin</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col lg:items-end gap-3 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                                    <div class="text-left lg:text-right">
                                        <p class="text-xs text-slate-400">Total Biaya</p>
                                        <p class="text-xl font-black text-blue-900">
                                            Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($booking->status === 'pending_payment')
                                            <a href="{{ route('payments.create', $booking->booking_code) }}"
                                               class="rounded-xl bg-[#0B1F4D] px-4 py-2 text-xs font-bold text-[#CCFF00] shadow-sm hover:bg-slate-800 transition">
                                                {{ $latestPayment ? 'Unggah Ulang Bukti' : 'Bayar Sekarang' }}
                                            </a>

                                            <form method="POST" action="{{ route('booking.cancel', $booking->booking_code) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        onclick="return confirm('Batalkan booking {{ $booking->booking_code }}?')"
                                                        class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('booking.show', $booking->booking_code) }}"
                                           class="rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                            Detail Booking
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <h4 class="mt-4 text-base font-bold text-slate-900">Belum Ada Booking</h4>
                            <p class="mt-1 text-xs text-slate-500">Anda belum memiliki riwayat pemesanan lapangan pada kategori ini.</p>
                            <a href="{{ route('booking.index') }}"
                               class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#0B1F4D] px-4 py-2 text-xs font-bold text-[#CCFF00] hover:bg-slate-800 transition">
                                Mulai Pesan Sekarang
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($bookings->hasPages())
                    <div class="mt-6 border-t border-slate-100 pt-4">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
