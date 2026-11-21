<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Management</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Kelola Venue & Lokasi</h2>
            </div>
            <a href="{{ route('admin.venues.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0B1F4D] px-4 py-2 text-sm font-bold text-[#CCFF00] shadow-sm hover:bg-slate-800 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Venue Baru
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
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

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('admin.venues.index') }}" class="flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-[260px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama venue, kota, atau alamat..."
                               class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="rounded-xl bg-[#0B1F4D] px-5 py-2.5 text-sm font-bold text-[#CCFF00] hover:bg-slate-800 transition">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.venues.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wider text-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nama Venue</th>
                                <th class="px-6 py-4">Kota & Alamat</th>
                                <th class="px-6 py-4">Kontak</th>
                                <th class="px-6 py-4">Total Lapangan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($venues as $venue)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        {{ $venue->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-800">{{ $venue->city }}</p>
                                        <p class="text-xs text-slate-400 truncate max-w-xs">{{ $venue->address }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <p class="font-mono text-slate-700">{{ $venue->phone ?: '-' }}</p>
                                        <p class="text-slate-400">{{ $venue->email ?: '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800">
                                            {{ $venue->courts_count }} Lapangan
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($venue->is_active)
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Aktif</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">Non-aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.venues.edit', $venue) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100">
                                                Edit & Jam
                                            </a>
                                            <form method="POST" action="{{ route('admin.venues.destroy', $venue) }}" onsubmit="return confirm('Hapus venue ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada venue terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($venues->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $venues->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
