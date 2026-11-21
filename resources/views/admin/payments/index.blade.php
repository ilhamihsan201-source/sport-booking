<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Management</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Riwayat & Verifikasi Pembayaran</h2>
            </div>
            <span class="text-xs font-bold text-slate-500">
                Total: {{ $payments->total() }} Transaksi
            </span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter Status Tabs --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.payments.index') }}"
                       class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ !request('status') ? 'bg-[#0B1F4D] text-[#CCFF00]' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Status
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}"
                       class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Menunggu Verifikasi
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'verified']) }}"
                       class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request('status') === 'verified' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Terverifikasi
                    </a>
                    <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}"
                       class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Ditolak
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wider text-slate-700">
                            <tr>
                                <th class="px-6 py-4">Kode Booking & Lapangan</th>
                                <th class="px-6 py-4">Pengirim & Referensi</th>
                                <th class="px-6 py-4">Nominal</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Waktu Kirim</th>
                                <th class="px-6 py-4 text-right">Bukti & Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.bookings.show', $payment->booking_id) }}" class="font-bold font-mono text-blue-700 hover:underline">
                                            {{ $payment->booking->booking_code ?? '#'.$payment->booking_id }}
                                        </a>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $payment->booking->court->name ?? '-' }} ({{ $payment->booking->court->sport->name ?? '-' }})
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-800">{{ $payment->sender_name ?: '-' }}</p>
                                        <p class="text-xs font-mono text-slate-400">{{ $payment->reference_number ?: '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 font-black text-slate-900">
                                        Rp{{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($payment->status === 'verified')
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Verified</span>
                                        @elseif($payment->status === 'rejected')
                                            <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800">Rejected</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $payment->paid_at?->format('d M Y, H:i') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($payment->proof_path)
                                                <a href="{{ route('admin.payments.proof', $payment) }}" target="_blank"
                                                   class="rounded-lg border border-blue-200 bg-white px-2.5 py-1 text-xs font-bold text-blue-700 hover:bg-blue-50">
                                                    Lihat Bukti
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.bookings.show', $payment->booking_id) }}"
                                               class="rounded-lg bg-[#0B1F4D] px-2.5 py-1 text-xs font-bold text-[#CCFF00] hover:bg-slate-800">
                                                Detail &rarr;
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($payments->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
