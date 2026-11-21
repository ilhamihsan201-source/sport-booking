<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Admin</p>
                <h2 class="text-2xl font-black text-slate-900">Edit Lapangan: {{ $court->name }}</h2>
            </div>
            <a href="{{ route('admin.courts.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
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

                <form method="POST" action="{{ route('admin.courts.update', $court) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Nama Lapangan *</label>
                            <input type="text" name="name" value="{{ old('name', $court->name) }}" required
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Venue / Lokasi *</label>
                            <select name="venue_id" required class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id', $court->venue_id) == $venue->id ? 'selected' : '' }}>
                                        {{ $venue->name }} ({{ $venue->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Cabang Olahraga *</label>
                            <select name="sport_id" required class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}" {{ old('sport_id', $court->sport_id) == $sport->id ? 'selected' : '' }}>
                                        {{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Harga Per Jam (Rp) *</label>
                            <input type="number" name="price_per_hour" value="{{ old('price_per_hour', (int)$court->price_per_hour) }}" required min="0" step="1000"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Kapasitas (Orang)</label>
                            <input type="number" name="capacity" value="{{ old('capacity', $court->capacity) }}" min="1"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Jenis Lapangan / Permukaan</label>
                            <input type="text" name="surface_type" value="{{ old('surface_type', $court->surface_type) }}"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Ukuran Lapangan</label>
                            <input type="text" name="size" value="{{ old('size', $court->size) }}"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Deskripsi Lapangan</label>
                        <textarea name="description" rows="3"
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $court->description) }}</textarea>
                    </div>

                    @if(isset($facilities) && $facilities->isNotEmpty())
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Fasilitas Pendukung Lapangan</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                @php
                                    $courtFacilityIds = $court->facilities->pluck('id')->toArray();
                                @endphp
                                @foreach($facilities as $facility)
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                        <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                               {{ in_array($facility->id, old('facilities', $courtFacilityIds)) ? 'checked' : '' }}
                                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $facility->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Status Operasional *</label>
                            <select name="status" required class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="active" {{ old('status', $court->status) === 'active' ? 'selected' : '' }}>Aktif (Dapat Dipesan)</option>
                                <option value="maintenance" {{ old('status', $court->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="inactive" {{ old('status', $court->status) === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                            </select>
                        </div>

                        <div class="flex items-center pt-7">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $court->is_featured) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-bold text-slate-700">Tampilkan di Pilihan Unggulan (Featured)</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.courts.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="rounded-xl bg-[#0B1F4D] px-6 py-2.5 text-sm font-bold text-[#CCFF00] hover:bg-slate-800 transition">
                            Perbarui Data Lapangan
                        </button>
                    </div>
                </form>
            </div>

            {{-- GALERI FOTO LAPANGAN --}}
            <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-black text-slate-900">Galeri Foto Lapangan</h3>
                        <p class="text-xs text-slate-500">Kelola foto-foto lapangan yang ditampilkan kepada customer.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-800">
                        {{ $court->images->count() }} Foto
                    </span>
                </div>

                {{-- Upload Form --}}
                <form method="POST" action="{{ route('admin.courts.images.store', $court) }}" enctype="multipart/form-data"
                      class="mb-8 p-5 rounded-2xl border border-dashed border-blue-300 bg-blue-50/30 flex flex-wrap items-center gap-4">
                    @csrf
                    <div class="flex-1 min-w-[240px]">
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Pilih File Foto (Bisa pilih hingga 5 foto sekaligus: JPG, PNG, WebP maks 5MB)
                        </label>
                        <input type="file" name="images[]" multiple required accept=".jpg,.jpeg,.png,.webp"
                               class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0B1F4D] file:text-[#CCFF00] hover:file:bg-slate-800 cursor-pointer">
                        @error('images')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 mt-4 sm:mt-0">
                            <input type="checkbox" name="is_primary" value="1" class="rounded border-slate-300 text-blue-600">
                            Jadikan Foto Pertama Sebagai Utama
                        </label>
                    </div>

                    <div>
                        <button type="submit" class="rounded-xl bg-[#0B1F4D] px-4 py-2 text-xs font-bold text-[#CCFF00] hover:bg-slate-800 transition">
                            Unggah Foto
                        </button>
                    </div>
                </form>

                {{-- Image Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @forelse($court->images as $img)
                        <div class="relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 shadow-xs">
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $img->alt_text }}"
                                 class="h-36 w-full object-cover">

                            @if($img->is_primary)
                                <span class="absolute top-2 left-2 rounded-lg bg-emerald-600 px-2 py-0.5 text-[10px] font-black uppercase text-white shadow">
                                    Utama
                                </span>
                            @endif

                            <div class="p-2.5 bg-white flex items-center justify-between gap-1 border-t border-slate-100">
                                @if(!$img->is_primary)
                                    <form method="POST" action="{{ route('admin.courts.images.primary', $img) }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold text-blue-600 hover:text-blue-800">
                                            Set Utama
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] font-bold text-emerald-700">Foto Utama</span>
                                @endif

                                <form method="POST" action="{{ route('admin.courts.images.destroy', $img) }}" onsubmit="return confirm('Hapus foto ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] font-bold text-red-600 hover:text-red-800">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-xs text-slate-400">
                            Belum ada foto yang diunggah untuk lapangan ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
