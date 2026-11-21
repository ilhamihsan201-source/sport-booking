<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Admin</p>
                <h2 class="text-2xl font-black text-slate-900">Tambah Venue Baru</h2>
            </div>
            <a href="{{ route('admin.venues.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                @if($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <ul class="list-inside list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.venues.store') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Nama Venue *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Contoh: Jakarta Padel & Mini Soccer Center"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Kota *</label>
                            <input type="text" name="city" value="{{ old('city') }}" required
                                   placeholder="Jakarta Selatan"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Alamat Lengkap *</label>
                        <input type="text" name="address" value="{{ old('address') }}" required
                               placeholder="Jl. Sudirman No. 45, Kebayoran Baru"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="081234567890"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Email Operasional</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="info@jakartasport.test"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Deskripsi Venue</label>
                        <textarea name="description" rows="3"
                                  placeholder="Deskripsi fasilitas umum, akses parkir, dll..."
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-bold text-slate-700">Aktifkan Venue untuk Reservasi Lapangan</span>
                        </label>
                    </div>

                    <div class="border-t border-slate-100 pt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.venues.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="rounded-xl bg-[#0B1F4D] px-6 py-2.5 text-sm font-bold text-[#CCFF00] hover:bg-slate-800 transition">
                            Simpan Venue & Jam Buka
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
