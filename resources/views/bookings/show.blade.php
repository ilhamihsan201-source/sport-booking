@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-12">
    <div class="mx-auto max-w-3xl">

        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800 text-center">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-lime-100 text-3xl">
                ✓
            </div>

            <h1 class="text-3xl font-bold text-slate-900">
                Booking berhasil dibuat!
            </h1>

            <p class="mt-2 text-slate-500">
                Simpan kode booking berikut untuk memeriksa reservasi kamu.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="bg-blue-900 p-6 text-white">
                <p class="text-sm font-medium text-blue-200">
                    KODE BOOKING
                </p>

                <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-2xl font-bold tracking-wider">
                        {{ $booking->booking_code }}
                    </h2>

                    <button
                        type="button"
                        onclick="navigator.clipboard.writeText('{{ $booking->booking_code }}')"
                        class="rounded-lg bg-white/15 px-4 py-2 text-sm font-semibold hover:bg-white/25"
                    >
                        Salin kode
                    </button>
                </div>
            </div>

            <div class="space-y-5 p-6">

                <div class="flex items-center justify-between gap-4">
                    <span class="text-slate-500 font-medium">Status Booking</span>

                    @php
                        $latestPayment = $booking->payments->sortByDesc('id')->first();
                    @endphp

                    @if($booking->status === 'confirmed')
                        <span class="rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-bold text-emerald-800">
                            Dikonfirmasi &bull; Siap Main
                        </span>
                    @elseif($booking->status === 'pending_payment')
                        @if($latestPayment && $latestPayment->status === 'pending')
                            <span class="rounded-full bg-blue-100 px-3.5 py-1 text-xs font-bold text-blue-800">
                                Sedang Menunggu Pemeriksaan Admin
                            </span>
                        @else
                            <span class="rounded-full bg-amber-100 px-3.5 py-1 text-xs font-bold text-amber-800">
                                Menunggu Pembayaran
                            </span>
                        @endif
                    @elseif($booking->status === 'completed')
                        <span class="rounded-full bg-blue-100 px-3.5 py-1 text-xs font-bold text-blue-800">
                            Selesai Digunakan
                        </span>
                    @elseif($booking->status === 'cancelled')
                        <span class="rounded-full bg-red-100 px-3.5 py-1 text-xs font-bold text-red-800">
                            Dibatalkan
                        </span>
                    @elseif($booking->status === 'expired')
                        <span class="rounded-full bg-gray-100 px-3.5 py-1 text-xs font-bold text-gray-700">
                            Kedaluwarsa
                        </span>
                    @endif
                </div>

                <hr>

                <div>
                    <p class="text-sm text-slate-500">Lapangan</p>
                    <p class="mt-1 font-bold text-slate-900">
                        {{ $booking->court->name }}
                    </p>
                    <p class="text-sm text-slate-500">
                        {{ $booking->court->sport->name }}
                        ·
                        {{ $booking->court->venue->name }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm text-slate-500">Tanggal</p>
                        <p class="mt-1 font-semibold">
                            {{ $booking->booking_date->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Jam bermain</p>
                        <p class="mt-1 font-semibold">
                            {{ substr($booking->start_time, 0, 5) }}
                            –
                            {{ substr($booking->end_time, 0, 5) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Durasi</p>
                        <p class="mt-1 font-semibold">
                            {{ $booking->duration_hours }} jam
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Nama pelanggan</p>
                        <p class="mt-1 font-semibold">
                            {{ $booking->customer_name }}
                        </p>
                    </div>
                </div>

                <hr>

                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-600">
                        Total pembayaran
                    </span>

                    <span class="text-2xl font-bold text-blue-800">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </span>
                </div>

                @if($booking->status === 'pending_payment')
                    @if($booking->expires_at && $booking->expires_at->isFuture())
                        @if($latestPayment && $latestPayment->status === 'pending')
                            <div class="rounded-xl bg-blue-50 p-4 text-xs text-blue-800 border border-blue-200">
                                Bukti transfer Anda telah dikirim dan <strong>sedang menunggu pemeriksaan admin</strong>. Status pesanan akan diperbarui setelah admin melakukan verifikasi.
                            </div>

                            <a href="{{ route('payments.create', $booking->booking_code) }}"
                               class="block rounded-xl border border-blue-200 bg-white px-5 py-3 text-center font-bold text-blue-700 transition hover:bg-blue-50 shadow-sm">
                                Periksa Bukti Pembayaran
                            </a>
                        @else
                            <div class="rounded-xl bg-amber-50 p-4 text-xs text-amber-800 border border-amber-200">
                                Reservasi sementara berlaku sampai
                                <strong>{{ $booking->expires_at->translatedFormat('d F Y, H:i') }}</strong>.
                                Silakan selesaikan pembayaran dan unggah bukti transfer sebelum batas waktu di atas agar jadwal tidak dibatalkan otomatis.
                            </div>

                            <a href="{{ route('payments.create', $booking->booking_code) }}"
                               class="block rounded-xl bg-lime-400 px-5 py-3 text-center font-black text-slate-950 transition hover:bg-lime-300 shadow-sm">
                                Lanjutkan ke Pembayaran
                            </a>
                        @endif

                        <form method="POST" action="{{ route('booking.cancel', $booking->booking_code) }}">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini? Slot jadwal akan dirilis kembali.')"
                                    class="w-full rounded-xl border border-red-200 bg-red-50/50 py-2.5 text-center text-xs font-bold text-red-600 hover:bg-red-50 transition">
                                Batalkan Pemesanan Ini
                            </button>
                        </form>
                    @else
                        <div class="rounded-xl bg-red-50 p-4 text-xs text-red-800 border border-red-200">
                            Batas waktu pembayaran untuk booking ini telah kedaluwarsa. Silakan lakukan reservasi baru.
                        </div>
                    @endif
                @elseif($booking->status === 'confirmed')
                    <div class="rounded-xl bg-emerald-50 p-4 text-xs text-emerald-800 border border-emerald-200">
                        Pemesanan Anda telah diverifikasi dan dikonfirmasi! Harap hadir di arena 10-15 menit sebelum jam bermain.
                    </div>
                @elseif($booking->status === 'cancelled')
                    <div class="rounded-xl bg-red-50 p-4 text-xs text-red-800 border border-red-200">
                        Pemesanan ini telah dibatalkan.
                        @if($booking->cancellation_reason)
                            <br><strong>Alasan:</strong> {{ $booking->cancellation_reason }}
                        @endif
                    </div>
                @endif

                <a href="{{ route('home') }}"
                   class="block rounded-xl bg-slate-900 px-5 py-3 text-center font-bold text-white transition hover:bg-slate-800">
                    Kembali ke Beranda
                </a>

            </div>
        </div>
    </div>
</div>
@endsection
