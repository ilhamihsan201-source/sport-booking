
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-blue-600">SPORTBOOKING ADMIN</p>
                <h2 class="text-2xl font-bold text-slate-900">
                    Detail Booking
                </h2>
            </div>

            <a href="{{ route('admin.bookings.index') }}"
               class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-300">Kode Booking</p>
                        <h1 class="mt-1 text-2xl font-black tracking-wide">
                            {{ $booking->booking_code }}
                        </h1>
                        <p class="mt-2 text-sm text-slate-300">
                            Dibuat {{ $booking->created_at?->format('d M Y, H:i') ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-white/10 px-4 py-3">
                        <p class="text-xs text-slate-300">Status Booking</p>
                        <p class="mt-1 font-bold">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-bold text-slate-900">
                        Informasi Customer
                    </h3>

                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Nama</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $booking->customer_name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Email</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $booking->customer_email ?: '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Nomor Telepon</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $booking->customer_phone }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Catatan Customer</dt>
                            <dd class="mt-1 whitespace-pre-line font-medium text-slate-800">
                                {{ $booking->customer_notes ?: '-' }}
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-bold text-slate-900">
                        Informasi Lapangan
                    </h3>

                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Lapangan</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $booking->court->name ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Olahraga</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $booking->court->sport->name ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Venue</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $booking->court->venue->name ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Tanggal Bermain</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Waktu</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ substr($booking->start_time, 0, 5) }}
                                -
                                {{ substr($booking->end_time, 0, 5) }}
                            </dd>
                        </div>
                    </dl>
                </section>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-slate-900">
                    Rincian Harga
                </h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Harga per jam</span>
                        <span class="font-semibold text-slate-900">
                            Rp{{ number_format($booking->price_per_hour, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Durasi</span>
                        <span class="font-semibold text-slate-900">
                            {{ $booking->duration_hours }} jam
                        </span>
                    </div>

                    <div class="border-t border-slate-100 pt-3">
                        <div class="flex justify-between gap-4">
                            <span class="font-bold text-slate-900">Total</span>
                            <span class="text-xl font-black text-blue-700">
                                Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- PANEL AKSI BOOKING --}}
            <section class="rounded-2xl border-2 border-blue-300 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black text-slate-900">
                        Aksi & Status Booking
                    </h3>
                    <span class="rounded-full px-3 py-1 text-xs font-black uppercase tracking-wider
                        {{ $booking->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' :
                           ($booking->status === 'pending_payment' ? 'bg-amber-100 text-amber-800' :
                           ($booking->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                        {{ strtoupper(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>

                @if($booking->status === 'pending_payment')
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                        <strong>Menunggu Pembayaran:</strong> Reservasi sementara aktif sampai {{ $booking->expires_at?->format('d M Y, H:i') ?? 'Batas waktu habis' }}.
                    </div>
                @endif

                @if(in_array($booking->status, ['pending_payment', 'confirmed']))
                    <div class="mt-5 border-t border-slate-100 pt-5">
                        @if($booking->status === 'confirmed')
                            <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 p-3 text-xs text-amber-900">
                                <strong>Penting (Kebijakan Refund):</strong> Pembatalan booking yang sudah berstatus <em>CONFIRMED</em> tidak melakukan refund otomatis oleh sistem. Proses pengembalian dana harus dilakukan secara manual oleh manajemen kepada customer via transfer bank.
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('admin.bookings.cancel', $booking) }}"
                              class="space-y-3">
                            @csrf

                            <label class="block text-sm font-bold text-slate-700">
                                Alasan Pembatalan Booking
                            </label>

                            <textarea name="cancellation_reason"
                                      required
                                      minlength="5"
                                      maxlength="1000"
                                      rows="2"
                                      class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500"
                                      placeholder="Masukkan alasan pembatalan..."></textarea>

                            <button type="submit"
                                    class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition"
                                    onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                                Batalkan Booking
                            </button>
                        </form>
                    </div>
                @endif

                @if($booking->status === 'confirmed')
                    @php
                        $bookingEnd = \Carbon\Carbon::parse(
                            $booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time
                        );
                    @endphp

                    <div class="mt-5 border-t border-slate-100 pt-5">
                        @if(now()->gte($bookingEnd))
                            <form method="POST"
                                  action="{{ route('admin.bookings.complete', $booking) }}">
                                @csrf

                                <button type="submit"
                                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 transition"
                                        onclick="return confirm('Tandai booking ini sebagai SELESAI?')">
                                    Selesaikan Booking (Waktu Bermain Usai)
                                </button>
                            </form>
                        @else
                            <div class="rounded-xl bg-blue-50 p-3 text-xs text-blue-800">
                                Waktu bermain berakhir pada: <strong>{{ $bookingEnd->format('d M Y, H:i') }}</strong>. Tombol penyelesaian booking akan aktif setelah waktu bermain terlampaui.
                            </div>
                        @endif
                    </div>
                @endif

                @if($booking->status === 'completed')
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="font-bold text-emerald-900">Booking Telah Selesai</p>
                        <p class="mt-1 text-xs text-emerald-800">Jadwal penggunaan lapangan telah selesai dilaksanakan. Tidak ada tindakan lebih lanjut yang diperlukan.</p>
                    </div>
                @elseif($booking->status === 'cancelled')
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
                        <p class="font-bold text-red-900">Booking Telah Dibatalkan</p>
                        <p class="mt-1 text-xs text-red-800">
                            <strong>Alasan:</strong> {{ $booking->cancellation_reason ?: 'Tidak ada alasan yang dicantumkan.' }}
                        </p>
                    </div>
                @elseif($booking->status === 'expired')
                    <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <p class="font-bold text-gray-800">Booking Kedaluwarsa</p>
                        <p class="mt-1 text-xs text-gray-600">Batas waktu pembayaran telah habis sebelum bukti transfer diverifikasi.</p>
                    </div>
                @endif
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900">
                        Riwayat Pembayaran
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                        {{ $payments->count() }} Transaksi
                    </span>
                </div>

                @forelse($payments as $payment)
                    <div class="mb-5 rounded-2xl border {{ $payment->status === 'verified' ? 'border-emerald-200 bg-emerald-50/20' : ($payment->status === 'rejected' ? 'border-red-200 bg-red-50/20' : 'border-blue-200 bg-blue-50/20') }} p-5 last:mb-0">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900">
                                        {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}
                                    </span>
                                    @if($payment->status === 'verified')
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                            Terverifikasi
                                        </span>
                                    @elseif($payment->status === 'rejected')
                                        <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                            Menunggu Verifikasi
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-slate-600">
                                    <span class="text-slate-400">Pengirim:</span> <strong>{{ $payment->sender_name ?: '-' }}</strong>
                                </p>
                                <p class="mt-0.5 text-sm text-slate-600">
                                    <span class="text-slate-400">No. Referensi:</span> <code class="bg-white/80 px-1.5 py-0.5 rounded text-xs text-slate-800">{{ $payment->reference_number ?: '-' }}</code>
                                </p>
                                <p class="mt-1 text-xs text-slate-400">
                                    Dikirim: {{ $payment->paid_at?->format('d M Y, H:i') ?? '-' }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-xs font-medium text-slate-500">Nominal Transfer</p>
                                <p class="text-xl font-black text-slate-900">
                                    Rp{{ number_format($payment->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Bukti Pembayaran preview / link --}}
                        <div class="mt-4 flex flex-wrap items-center gap-3 pt-3 border-t border-slate-200/60">
                            @if($payment->proof_path)
                                <a href="{{ route('admin.payments.proof', $payment) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-blue-300 bg-white px-3.5 py-2 text-xs font-bold text-blue-700 shadow-sm hover:bg-blue-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Buka Bukti Transfer
                                </a>
                            @else
                                <span class="text-xs text-slate-400">Tidak ada lampiran bukti.</span>
                            @endif

                            @if($payment->reviewer)
                                <span class="text-xs text-slate-500 ml-auto">
                                    Diperiksa oleh <strong>{{ $payment->reviewer->name }}</strong>
                                    pada {{ $payment->verified_at?->format('d M Y, H:i') }}
                                </span>
                            @endif
                        </div>

                        @if($payment->rejection_reason)
                            <div class="mt-3 rounded-xl bg-red-100/70 p-3 text-xs text-red-800">
                                <strong>Alasan Penolakan:</strong> {{ $payment->rejection_reason }}
                            </div>
                        @endif

                        {{-- AKSI VERIFIKASI / TOLAK JIKA STATUS PENDING --}}
                        @if($payment->status === 'pending')
                            <div class="mt-4 border-t border-slate-200/70 pt-4" x-data="{ showReject: false }">
                                <div class="flex flex-wrap items-center gap-3">
                                    <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Konfirmasi dan verifikasi pembayaran sebesar Rp{{ number_format($payment->amount, 0, ',', '.') }}?')"
                                                class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Verifikasi & Setujui
                                        </button>
                                    </form>

                                    <button type="button"
                                            @click="showReject = !showReject"
                                            class="rounded-xl border border-red-300 bg-white px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition">
                                        Tolak Pembayaran...
                                    </button>
                                </div>

                                <div x-show="showReject" x-cloak class="mt-3 rounded-xl border border-red-200 bg-red-50/50 p-4">
                                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="space-y-3">
                                        @csrf
                                        <label class="block text-xs font-bold text-red-900">
                                            Alasan Penolakan (akan dikirimkan ke customer):
                                        </label>
                                        <textarea name="rejection_reason"
                                                  required
                                                  minlength="5"
                                                  maxlength="1000"
                                                  rows="2"
                                                  class="w-full rounded-xl border-red-300 text-xs focus:border-red-500 focus:ring-red-500"
                                                  placeholder="Contoh: Bukti transfer buram / nominal tidak sesuai total invoice"></textarea>
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                    onclick="return confirm('Tolak pembayaran ini?')"
                                                    class="rounded-xl bg-red-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-red-700">
                                                Kirim Penolakan
                                            </button>
                                            <button type="button" @click="showReject = false" class="text-xs text-slate-500 hover:text-slate-700 font-medium">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">
                        Belum ada bukti pembayaran untuk booking ini.
                    </p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
