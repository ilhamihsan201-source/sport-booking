<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">
                    SportBooking Management
                </p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                    Daftar Pelanggan Terdaftar
                </h2>
            </div>
            <p class="text-xs font-semibold text-slate-500">
                Total: {{ $customers->total() }} Pelanggan
            </p>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Filter and Search --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-[260px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama, email, atau nomor HP pelanggan..."
                               class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                            class="rounded-xl bg-[#0B1F4D] px-5 py-2.5 text-sm font-bold text-[#CCFF00] hover:bg-slate-800 transition">
                        Cari Pelanggan
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.customers.index') }}"
                           class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Customers Table --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wider text-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nama Pelanggan</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Nomor HP</th>
                                <th class="px-6 py-4">Total Booking</th>
                                <th class="px-6 py-4">Terdaftar Pada</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($customers as $customer)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-sm font-black text-blue-900">
                                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $customer->name }}</p>
                                                <p class="text-xs text-slate-400">ID: #{{ $customer->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700">
                                        {{ $customer->email }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                        {{ $customer->phone ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800">
                                            {{ $customer->bookings_count }} Transaksi
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $customer->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.customers.show', $customer) }}"
                                           class="rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                            Lihat Aktivitas &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                        Tidak ada pelanggan ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
