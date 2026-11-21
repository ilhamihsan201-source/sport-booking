<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">
                    SportBooking Management
                </p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                    Kelola Lapangan Olahraga
                </h2>
            </div>

            <a href="{{ route('admin.courts.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-[#0B1F4D] px-4 py-2 text-sm font-bold text-[#CCFF00] shadow-sm hover:bg-slate-800 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Lapangan Baru
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

            {{-- Filter and Search --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('admin.courts.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600">Cari Nama</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Nama lapangan..."
                               class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600">Cabang Olahraga</label>
                        <select name="sport_id" class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Olahraga</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600">Status Operasional</label>
                        <select name="status" class="mt-1 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="rounded-xl bg-[#0B1F4D] px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-800 transition">
                            Filter
                        </button>
                        <a href="{{ route('admin.courts.index') }}"
                           class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Courts Table --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wider text-slate-700">
                            <tr>
                                <th class="px-6 py-4">Lapangan & Venue</th>
                                <th class="px-6 py-4">Olahraga</th>
                                <th class="px-6 py-4">Harga / Jam</th>
                                <th class="px-6 py-4">Kapasitas</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($courts as $court)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-900">{{ $court->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $court->venue->name ?? '-' }} &bull; {{ $court->venue->city ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">
                                            {{ $court->sport->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-black text-slate-900">
                                        Rp{{ number_format($court->price_per_hour, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $court->capacity ? $court->capacity . ' Orang' : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-start gap-1">
                                            @if($court->status === 'active')
                                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Aktif</span>
                                            @elseif($court->status === 'maintenance')
                                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Maintenance</span>
                                            @else
                                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">Non-aktif</span>
                                            @endif

                                            @if(($court->bookings_count ?? 0) > 0)
                                                <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-800 border border-amber-200"
                                                      title="Lapangan memiliki {{ $court->bookings_count }} riwayat booking.">
                                                    {{ $court->bookings_count }} Riwayat Booking
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Tombol Toggle Aktif / Non-aktif --}}
                                            <form method="POST" action="{{ route('admin.courts.toggle-status', $court) }}">
                                                @csrf
                                                @if($court->status === 'active')
                                                    <button type="submit"
                                                            class="rounded-lg border border-amber-200 bg-amber-50/80 px-2.5 py-1.5 text-xs font-bold text-amber-800 hover:bg-amber-100 transition"
                                                            title="Nonaktifkan lapangan agar tidak muncul di katalog customer">
                                                        Nonaktifkan
                                                    </button>
                                                @else
                                                    <button type="submit"
                                                            class="rounded-lg border border-emerald-200 bg-emerald-50/80 px-2.5 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition"
                                                            title="Aktifkan kembali agar dapat dipesan customer">
                                                        Aktifkan
                                                    </button>
                                                @endif
                                            </form>

                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('admin.courts.edit', $court) }}"
                                               class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                                Edit
                                            </a>

                                            {{-- Tombol Hapus --}}
                                            <form method="POST" action="{{ route('admin.courts.destroy', $court) }}"
                                                  onsubmit="return confirm('{{ ($court->bookings_count ?? 0) > 0 ? "Peringatan: Lapangan ini memiliki riwayat booking. Sistem akan menolak penghapusan dan menyarankan penonaktifan. Tetap lanjutkan?" : "Hapus lapangan " . $court->name . " secara permanen?" }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 transition"
                                                        title="{{ ($court->bookings_count ?? 0) > 0 ? 'Memiliki riwayat booking' : 'Hapus lapangan' }}">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                        Tidak ada data lapangan ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($courts->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $courts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
