<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Admin</p>
                <h2 class="text-2xl font-black text-slate-900">Edit Venue: {{ $venue->name }}</h2>
            </div>
            <a href="{{ route('admin.venues.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">
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

                <form method="POST" action="{{ route('admin.venues.update', $venue) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Nama Venue *</label>
                            <input type="text" name="name" value="{{ old('name', $venue->name) }}" required
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Kota *</label>
                            <input type="text" name="city" value="{{ old('city', $venue->city) }}" required
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Alamat Lengkap *</label>
                        <input type="text" name="address" value="{{ old('address', $venue->address) }}" required
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $venue->phone) }}"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Email Operasional</label>
                            <input type="email" name="email" value="{{ old('email', $venue->email) }}"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Deskripsi Venue</label>
                        <textarea name="description" rows="3"
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $venue->description) }}</textarea>
                    </div>

                    {{-- Jam Operasional Harian --}}
                    <div class="border-t border-slate-100 pt-6">
                        <h4 class="text-base font-black text-slate-900 mb-3">Jam Operasional Harian</h4>
                        <p class="text-xs text-slate-500 mb-4">Atur jam buka dan tutup untuk setiap hari:</p>

                        @php
                            $days = [
                                0 => 'Minggu',
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                            ];
                            $hoursMap = $venue->operatingHours->keyBy('day_of_week');
                        @endphp

                        <div class="space-y-3">
                            @foreach($days as $dayNumber => $dayName)
                                @php
                                    $hour = $hoursMap->get($dayNumber);
                                    $opens = $hour ? substr($hour->opens_at, 0, 5) : '06:00';
                                    $closes = $hour ? substr($hour->closes_at, 0, 5) : '23:00';
                                    $isClosed = $hour ? $hour->is_closed : false;
                                @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-4 items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/60">
                                    <span class="font-bold text-sm text-slate-800">{{ $dayName }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-slate-500">Buka:</span>
                                        <input type="time" name="hours[{{ $dayNumber }}][opens_at]" value="{{ old('hours.'.$dayNumber.'.opens_at', $opens) }}"
                                               class="rounded-lg border-slate-300 text-xs py-1">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-slate-500">Tutup:</span>
                                        <input type="time" name="hours[{{ $dayNumber }}][closes_at]" value="{{ old('hours.'.$dayNumber.'.closes_at', $closes) }}"
                                               class="rounded-lg border-slate-300 text-xs py-1">
                                    </div>
                                    <div>
                                        <label class="flex items-center gap-2 text-xs text-slate-600">
                                            <input type="checkbox" name="hours[{{ $dayNumber }}][is_closed]" value="1" {{ $isClosed ? 'checked' : '' }}
                                                   class="rounded text-red-600">
                                            Tutup Hari Ini
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer pt-3">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $venue->is_active) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-bold text-slate-700">Venue Aktif</span>
                        </label>
                    </div>

                    <div class="border-t border-slate-100 pt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.venues.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="rounded-xl bg-[#0B1F4D] px-6 py-2.5 text-sm font-bold text-[#CCFF00] hover:bg-slate-800 transition">
                            Perbarui Data Venue & Jam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
