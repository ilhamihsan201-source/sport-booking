
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">
                    SportBooking Management
                </p>

                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                    Dashboard Administrator
                </h2>
            </div>

            <div class="flex items-center gap-2 text-sm text-slate-500">
                <span class="h-2 w-2 rounded-full bg-lime-500"></span>
                Sistem aktif
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">

            {{-- Welcome banner --}}
            <section class="relative overflow-hidden rounded-3xl bg-[#0B1F4D] p-6 text-white shadow-xl sm:p-8">
                <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>
                <div class="absolute -bottom-24 right-1/3 h-48 w-48 rounded-full bg-lime-400/10 blur-3xl"></div>

                <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-200">
                            Selamat datang kembali,
                        </p>

                        <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                            {{ auth()->user()->name }}
                            <span class="text-[#CCFF00]">.</span>
                        </h1>

                        <p class="mt-3 max-w-xl text-sm leading-6 text-blue-100">
                            Pantau aktivitas booking, pembayaran, dan operasional
                            lapangan SportBooking dari satu tempat.
                        </p>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#CCFF00] text-xl font-black text-[#0B1F4D]">
                            SB
                        </div>

                        <div>
                            <p class="text-xs text-blue-200">Akses akun</p>
                            <p class="font-bold">Administrator</p>
                            <p class="text-xs text-lime-300">Role: admin</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Main statistics --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Total bookings --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14H4V6a1 1 0 011-1z"/>
                            </svg>
                        </div>

                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                            Semua
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-medium text-slate-500">
                        Total Booking
                    </p>

                    <h3 class="mt-1 text-3xl font-black text-slate-900">
                        {{ number_format($totalBookings) }}
                    </h3>

                    <p class="mt-2 text-xs text-slate-500">
                        {{ number_format($todayBookings) }} booking hari ini
                    </p>
                </div>

                {{-- Revenue --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-lime-50 text-lime-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-2.2 0-4 1.1-4 2.5S9.8 13 12 13s4 1.1 4 2.5S14.2 18 12 18m0-12v12m9-6a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        <span class="rounded-full bg-lime-50 px-3 py-1 text-xs font-bold text-lime-700">
                            Verified
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-medium text-slate-500">
                        Total Pendapatan
                    </p>

                    <h3 class="mt-1 text-2xl font-black text-slate-900">
                        Rp{{ number_format($totalRevenue, 0, ',', '.') }}
                    </h3>

                    <p class="mt-2 text-xs text-slate-500">
                        Dari pembayaran terverifikasi
                    </p>
                </div>

                {{-- Customers --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm10 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                            </svg>
                        </div>

                        <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700">
                            Customer
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-medium text-slate-500">
                        Total Pelanggan
                    </p>

                    <h3 class="mt-1 text-3xl font-black text-slate-900">
                        {{ number_format($totalCustomers) }}
                    </h3>

                    <p class="mt-2 text-xs text-slate-500">
                        Akun dengan role customer
                    </p>
                </div>

                {{-- Courts --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5h18v14H3zM3 12h18M12 5v14"/>
                            </svg>
                        </div>

                        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-bold text-orange-700">
                            Lapangan
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-medium text-slate-500">
                        Total Lapangan
                    </p>

                    <h3 class="mt-1 text-3xl font-black text-slate-900">
                        {{ number_format($totalCourts) }}
                    </h3>

                    <p class="mt-2 text-xs text-slate-500">
                        {{ number_format($activeCourts) }} lapangan aktif
                    </p>
                </div>
            </section>

            {{-- Secondary statistics --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Menunggu Pembayaran
                        </p>

                        <p class="mt-2 text-2xl font-black text-slate-900">
                            {{ number_format($pendingBookings) }}
                        </p>
                    </div>

                    <span class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">
                        Pending
                    </span>
                </div>

                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Booking Terkonfirmasi
                        </p>

                        <p class="mt-2 text-2xl font-black text-slate-900">
                            {{ number_format($confirmedBookings) }}
                        </p>
                    </div>

                    <span class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700">
                        Confirmed
                    </span>
                </div>

                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Perlu Verifikasi
                        </p>

                        <p class="mt-2 text-2xl font-black text-slate-900">
                            {{ number_format($pendingPayments) }}
                        </p>
                    </div>

                    <span class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">
                        Payment
                    </span>
                </div>
            </section>

            {{-- Charts --}}
            <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                {{-- Revenue chart --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">
                                Ringkasan Pendapatan
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Pendapatan terverifikasi selama 6 bulan terakhir
                            </p>
                        </div>

                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                            6 Bulan
                        </span>
                    </div>

                    <div class="mt-8 flex h-56 items-end justify-between gap-3">
                        @foreach ($chart as $item)
                            <div class="flex h-full flex-1 flex-col items-center justify-end gap-3">
                                <div class="flex h-full w-full items-end justify-center">
                                    <div
                                        class="w-full max-w-12 rounded-t-xl bg-blue-600 transition-all"
                                        style="height: {{ $item['revenue'] > 0 ? max(($item['revenue'] / $maxRevenue) * 100, 5) : 3 }}%"
                                        title="Rp{{ number_format($item['revenue'], 0, ',', '.') }}"
                                    ></div>
                                </div>

                                <span class="text-xs font-semibold text-slate-500">
                                    {{ $item['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
                        <span class="h-3 w-3 rounded-sm bg-blue-600"></span>

                        <span class="text-xs font-medium text-slate-500">
                            Pendapatan terverifikasi
                        </span>
                    </div>
                </div>

                {{-- Booking summary --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-black text-slate-900">
                        Aktivitas Booking
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Jumlah booking per bulan
                    </p>

                    <div class="mt-6 space-y-5">
                        @foreach ($chart as $item)
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-600">
                                        {{ $item['label'] }}
                                    </span>

                                    <span class="text-sm font-bold text-slate-900">
                                        {{ number_format($item['bookings']) }}
                                    </span>
                                </div>

                                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                    <div
                                        class="h-full rounded-full bg-[#CCFF00]"
                                        style="width: {{ $item['bookings'] > 0 ? min(($item['bookings'] / max(collect($chart)->max('bookings'), 1)) * 100, 100) : 0 }}%"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Recent bookings --}}
            <section id="booking-terbaru" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">
                            Booking Terbaru
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Aktivitas reservasi lapangan terbaru
                        </p>
                    </div>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        {{ $recentBookings->count() }} data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-left">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-4 font-bold">Kode Booking</th>
                                <th class="px-6 py-4 font-bold">Pelanggan</th>
                                <th class="px-6 py-4 font-bold">Lapangan</th>
                                <th class="px-6 py-4 font-bold">Tanggal</th>
                                <th class="px-6 py-4 font-bold">Total</th>
                                <th class="px-6 py-4 font-bold">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentBookings as $booking)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-blue-700">
                                            {{ $booking->booking_code }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-slate-800">
                                            {{ $booking->customer_name }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ $booking->court_name }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                                        <br>
                                        <span class="text-xs text-slate-400">
                                            {{ substr($booking->start_time, 0, 5) }}
                                            -
                                            {{ substr($booking->end_time, 0, 5) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($booking->status === 'confirmed')
                                            <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                                Confirmed
                                            </span>
                                        @elseif ($booking->status === 'pending_payment')
                                            <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                                                Pending
                                            </span>
                                        @elseif ($booking->status === 'completed')
                                            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
                                                Completed
                                            </span>
                                        @elseif ($booking->status === 'cancelled')
                                            <span class="rounded-full bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700">
                                                Cancelled
                                            </span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <p class="font-semibold text-slate-700">
                                            Belum ada booking
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Booking pelanggan akan muncul di sini.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Payment review --}}
            <section id="payment-review" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">
                            Pembayaran Menunggu Verifikasi
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Pembayaran yang sudah dikirim pelanggan
                        </p>
                    </div>

                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                        {{ $pendingPayments }} menunggu
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($paymentReviews as $payment)
                        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>

                                <div>
                                    <p class="font-bold text-slate-900">
                                        {{ $payment->customer_name }}
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $payment->booking_code }}
                                        ·
                                        {{ $payment->court_name }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Pengirim: {{ $payment->sender_name ?: '-' }}
                                        ·
                                        Ref: {{ $payment->reference_number ?: '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="sm:text-right">
                                <p class="text-lg font-black text-slate-900">
                                    Rp{{ number_format($payment->amount, 0, ',', '.') }}
                                </p>

                                <p class="mt-1 text-xs font-semibold text-amber-600">
                                    Menunggu pemeriksaan
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-lime-50 text-lime-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>

                            <p class="mt-3 font-bold text-slate-800">
                                Semua pembayaran sudah diperiksa
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Tidak ada pembayaran yang menunggu verifikasi.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Footer --}}
            <footer class="flex flex-col gap-2 border-t border-slate-200 py-5 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                <p>
                    © {{ date('Y') }} SportBooking. Admin Management System.
                </p>

                <p>
                    Logged in as {{ auth()->user()->email }}
                </p>
            </footer>

        </div>
    </div>
</x-app-layout>
