<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-blue-600">SPORTBOOKING ADMIN</p>
                <h2 class="text-2xl font-bold text-slate-900">
                    Manajemen Booking
                </h2>
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('admin.bookings.index') }}"
                      class="grid gap-4 md:grid-cols-4">

                    <div class="md:col-span-2">
                        <label for="search" class="mb-1 block text-sm font-semibold text-slate-700">
                            Cari booking
                        </label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Kode booking, nama, atau telepon..."
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="status" class="mb-1 block text-sm font-semibold text-slate-700">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Semua status</option>
                            <option value="pending_payment" @selected(request('status') === 'pending_payment')>
                                Menunggu pembayaran
                            </option>
                            <option value="confirmed" @selected(request('status') === 'confirmed')>
                                Dikonfirmasi
                            </option>
                            <option value="completed" @selected(request('status') === 'completed')>
                                Selesai
                            </option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>
                                Dibatalkan
                            </option>
                            <option value="expired" @selected(request('status') === 'expired')>
                                Kedaluwarsa
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="date" class="mb-1 block text-sm font-semibold text-slate-700">
                            Tanggal bermain
                        </label>
                        <input
                            id="date"
                            type="date"
                            name="date"
                            value="{{ request('date') }}"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div class="flex flex-wrap gap-2 md:col-span-4">
                        <button type="submit"
                                class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                            Cari Booking
                        </button>

                        <a href="{{ route('admin.bookings.index') }}"
                           class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <h3 class="font-bold text-slate-900">Daftar Booking</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Total hasil: {{ $bookings->total() }} booking
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-4">Booking</th>
                                <th class="px-5 py-4">Customer</th>
                                <th class="px-5 py-4">Lapangan</th>
                                <th class="px-5 py-4">Jadwal</th>
                                <th class="px-5 py-4">Total</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($bookings as $booking)
                                @php
                                    $statusClasses = match($booking->status) {
                                        'confirmed' => 'bg-green-100 text-green-700',
                                        'pending_payment' => 'bg-amber-100 text-amber-700',
                                        'completed' => 'bg-blue-100 text-blue-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'expired' => 'bg-slate-100 text-slate-600',
                                        default => 'bg-slate-100 text-slate-600',
                                    };

                                    $statusLabels = [
                                        'pending_payment' => 'Menunggu pembayaran',
                                        'confirmed' => 'Dikonfirmasi',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                        'expired' => 'Kedaluwarsa',
                                    ];
                                @endphp

                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-slate-900">
                                            {{ $booking->booking_code }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            #{{ $booking->id }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-800">
                                            {{ $booking->customer_name }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $booking->customer_phone }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-800">
                                            {{ $booking->court_name ?? '-' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $booking->sport_name ?? '-' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-800">
                                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ substr($booking->start_time, 0, 5) }}
                                            -
                                            {{ substr($booking->end_time, 0, 5) }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-4 font-bold text-slate-900">
                                        Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClasses }}">
                                            {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('admin.bookings.show', $booking) }}"
                                           class="inline-flex rounded-lg bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center">
                                        <p class="font-semibold text-slate-700">
                                            Booking tidak ditemukan
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            Coba ubah kata pencarian atau filter.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
