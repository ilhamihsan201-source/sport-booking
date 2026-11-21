
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-12">
    <div class="mx-auto max-w-3xl">

        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-700">
                SportBooking Payment
            </p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                Pembayaran Booking
            </h1>
            <p class="mt-2 text-slate-500">
                Selesaikan pembayaran dan kirim bukti transfer.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="bg-blue-900 p-6 text-white">
                <p class="text-sm text-blue-200">KODE BOOKING</p>
                <h2 class="mt-1 text-2xl font-bold">
                    {{ $booking->booking_code }}
                </h2>
            </div>

            <div class="space-y-5 p-6">

                <div>
                    <p class="text-sm text-slate-500">Lapangan</p>
                    <p class="font-bold">{{ $booking->court->name }}</p>
                    <p class="text-sm text-slate-500">
                        {{ $booking->court->sport->name }}
                        · {{ $booking->court->venue->name }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Tanggal</p>
                        <p class="font-semibold">
                            {{ $booking->booking_date->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Jam</p>
                        <p class="font-semibold">
                            {{ substr($booking->start_time, 0, 5) }}
                            – {{ substr($booking->end_time, 0, 5) }}
                        </p>
                    </div>
                </div>

                <div class="rounded-xl bg-blue-50 p-5">
                    <p class="text-sm font-medium text-blue-800">
                        Total yang harus dibayar
                    </p>
                    <p class="mt-1 text-3xl font-extrabold text-blue-900">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50/50 to-white p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Tujuan Transfer Pembayaran
                        </h3>
                        @if(config('sportbooking.payment_mode') === 'demo')
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-amber-800">
                                Mode Simulasi / Rekening Demo
                            </span>
                        @endif
                    </div>

                    @if(config('sportbooking.payment_mode') === 'demo')
                        <p class="mt-2 text-xs text-amber-700 bg-amber-50 p-2.5 rounded-xl border border-amber-200/80">
                            <strong>Catatan Portofolio:</strong> Rekening di bawah ini merupakan akun simulasi/demo untuk pengujian dan demonstrasi sistem. Untuk pengujian, silakan unggah gambar struk/bukti transfer uji coba.
                        </p>
                    @else
                        <p class="mt-1 text-xs text-slate-600">
                            Silakan transfer tepat sebesar nominal di atas ke salah satu rekening tujuan SportBooking:
                        </p>
                    @endif

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach(config('sportbooking.bank_accounts', []) as $key => $account)
                            <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-slate-900 tracking-wider">{{ $account['bank_name'] }}</span>
                                    <span class="text-[10px] font-bold uppercase rounded bg-blue-100 text-blue-800 px-1.5 py-0.5">
                                        {{ $account['is_demo'] ? 'Demo' : 'Transfer Bank' }}
                                    </span>
                                </div>
                                <p class="mt-2 text-base font-black text-slate-800 tracking-wider font-mono">{{ $account['account_number'] }}</p>
                                <p class="text-xs text-slate-500">a.n. {{ $account['account_holder'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @php
                    $latestPayment = $booking->payments->sortByDesc('id')->first();
                @endphp

                @if($latestPayment && $latestPayment->status === 'pending')
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <p class="font-bold text-blue-900">
                            Bukti pembayaran sudah dikirim
                        </p>
                        <p class="mt-1 text-sm text-blue-800">
                            Status: <strong class="font-bold">Sedang menunggu pemeriksaan admin</strong>.
                        </p>
                        <div class="mt-3 flex gap-3">
                            <a href="{{ route('booking.show', $booking->booking_code) }}"
                               class="rounded-lg bg-blue-800 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-blue-900">
                                Lihat Detail Reservasi
                            </a>
                            @auth
                                <a href="{{ route('customer.dashboard') }}"
                                   class="rounded-lg border border-blue-300 bg-white px-3.5 py-2 text-xs font-bold text-blue-800 transition hover:bg-blue-100">
                                    Buka Dashboard Saya
                                </a>
                            @endauth
                        </div>
                    </div>

                @elseif($latestPayment && $latestPayment->status === 'verified')
                    <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                        <p class="font-bold text-green-900">
                            Pembayaran sudah diverifikasi
                        </p>
                        <p class="mt-1 text-sm text-green-800">
                            Terima kasih. Silakan periksa status booking kamu.
                        </p>
                    </div>

                @elseif($booking->status !== 'pending_payment')
                    <div class="rounded-xl border border-slate-200 bg-slate-100 p-4">
                        <p class="font-bold text-slate-800">
                            Booking tidak menerima pembayaran baru
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Status booking: {{ $booking->status }}
                        </p>
                    </div>

                @elseif(!$booking->expires_at || !$booking->expires_at->isFuture())
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                        <p class="font-bold text-red-800">
                            Masa berlaku booking telah habis
                        </p>
                        <p class="mt-1 text-sm text-red-700">
                            Silakan buat pemesanan baru.
                        </p>
                    </div>

                @else
                    <form
                        action="{{ route('payments.store', $booking->booking_code) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-5"
                    >
                        @csrf

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Nama pengirim rekening
                            </label>
                            <input
                                type="text"
                                name="sender_name"
                                value="{{ old('sender_name') }}"
                                required
                                minlength="3"
                                maxlength="100"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:outline-none"
                                placeholder="Nama sesuai rekening"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Nomor referensi transfer (opsional)
                            </label>
                            <input
                                type="text"
                                name="reference_number"
                                value="{{ old('reference_number') }}"
                                maxlength="100"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:outline-none"
                                placeholder="Nomor referensi transaksi"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Bukti transfer
                            </label>
                            <input
                                type="file"
                                name="proof"
                                accept=".jpg,.jpeg,.png,.pdf"
                                required
                                class="w-full rounded-xl border border-slate-300 p-3"
                            >
                            <p class="mt-2 text-xs text-slate-500">
                                JPG, JPEG, PNG atau PDF. Maksimal 5 MB.
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-blue-800 px-5 py-4 font-bold text-white transition hover:bg-blue-900"
                        >
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
