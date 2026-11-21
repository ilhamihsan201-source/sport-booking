<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Admin</p>
                <h2 class="text-2xl font-black text-slate-900">Profil Pelanggan: {{ $customer->name }}</h2>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Customer Info Card --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#0B1F4D] text-2xl font-black text-[#CCFF00]">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">{{ $customer->name }}</h3>
                            <p class="text-sm text-slate-500">{{ $customer->email }} &bull; {{ $customer->phone ?: 'No HP belum terisi' }}</p>
                            <p class="mt-1 text-xs text-slate-400">Bergabung sejak {{ $customer->created_at->format('d F Y') }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-blue-50/60 p-4 border border-blue-100 text-center sm:text-right">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Total Pemesanan</p>
                        <p class="text-3xl font-black text-blue-950">{{ $customer->bookings_count }}</p>
                    </div>
                </div>
            </div>

            {{-- Booking History by Customer --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-black text-slate-900 mb-4">
                    Riwayat Pemesanan Lapangan
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wider text-slate-700">
                            <tr>
                                <th class="px-5 py-3">Kode Booking</th>
                                <th class="px-5 py-3">Lapangan & Olahraga</th>
                                <th class="px-5 py-3">Tanggal & Jam</th>
                                <th class="px-5 py-3">Total Biaya</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($bookings as $booking)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-5 py-3.5 font-bold font-mono text-slate-900">
                                        {{ $booking->booking_code }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-bold text-slate-800">{{ $booking->court->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">{{ $booking->court->sport->name ?? '-' }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-600">
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</p>
                                        <p class="text-slate-400">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 font-bold text-slate-900">
                                        Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($booking->status === 'confirmed')
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Dikonfirmasi</span>
                                        @elseif($booking->status === 'pending_payment')
                                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Menunggu Bayar</span>
                                        @elseif($booking->status === 'completed')
                                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">Selesai</span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-700">{{ ucfirst($booking->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('admin.bookings.show', $booking) }}"
                                           class="rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-bold text-blue-700 hover:bg-blue-50">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                        Belum ada riwayat pemesanan untuk pelanggan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($bookings->hasPages())
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
