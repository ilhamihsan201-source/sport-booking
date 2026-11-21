<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Management</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Kelola Fasilitas Lapangan</h2>
            </div>
            <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0B1F4D] px-4 py-2 text-sm font-bold text-[#CCFF00] shadow-sm hover:bg-slate-800 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Fasilitas
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

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wider text-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nama Fasilitas</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4">Jumlah Lapangan</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($facilities as $facility)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900 flex items-center gap-2">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-700 text-xs font-bold">
                                            ✓
                                        </span>
                                        {{ $facility->name }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $facility->description ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800">
                                            {{ $facility->courts_count }} Lapangan Terhubung
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.facilities.edit', $facility) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.facilities.destroy', $facility) }}" onsubmit="return confirm('Hapus fasilitas ini?')">
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
                                    <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada fasilitas terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($facilities->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $facilities->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
