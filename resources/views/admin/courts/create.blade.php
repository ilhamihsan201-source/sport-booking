<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Admin</p>
                <h2 class="text-2xl font-black text-slate-900">Tambah Lapangan Olahraga</h2>
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

                <form method="POST" action="{{ route('admin.courts.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Nama Lapangan *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Contoh: Padel Court Panoramic C"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Venue / Lokasi *</label>
                            <select name="venue_id" required class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Venue</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
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
                                <option value="">Pilih Olahraga</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                                        {{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Harga Per Jam (Rp) *</label>
                            <input type="number" name="price_per_hour" value="{{ old('price_per_hour') }}" required min="0" step="1000"
                                   placeholder="250000"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Kapasitas (Orang)</label>
                            <input type="number" name="capacity" value="{{ old('capacity', 4) }}" min="1"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Jenis Lapangan / Permukaan</label>
                            <input type="text" name="surface_type" value="{{ old('surface_type') }}"
                                   placeholder="Rumput Sintetis / Kaca Panoramic"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Ukuran Lapangan</label>
                            <input type="text" name="size" value="{{ old('size') }}"
                                   placeholder="20m x 10m"
                                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Deskripsi Lapangan</label>
                        <textarea name="description" rows="3"
                                  placeholder="Deskripsi spesifikasi lapangan, rumput, pencahayaan..."
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    </div>

                    @if(isset($facilities) && $facilities->isNotEmpty())
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Fasilitas Pendukung Lapangan</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                @foreach($facilities as $facility)
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                        <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                               {{ in_array($facility->id, old('facilities', [])) ? 'checked' : '' }}
                                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span>{{ $facility->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- FOTO LAPANGAN DARI BERBAGAI ARAH & FASILITAS --}}
                    <div class="rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50/50 p-5">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <label class="block text-sm font-black text-slate-900">
                                    Unggah Foto Lapangan (Berbagai Arah & Fasilitas)
                                </label>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Bisa pilih hingga 5 foto sekaligus: tampak depan, sudut net/kaca, bird view, dan fasilitas seperti ruang ganti/shower (JPG, PNG, WebP maks 5MB per file).
                                </p>
                            </div>
                            <span class="rounded-lg bg-blue-100 px-2.5 py-1 text-[11px] font-bold text-blue-800">
                                Multi-upload
                            </span>
                        </div>

                        <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp"
                               class="mt-2 w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0B1F4D] file:text-[#CCFF00] hover:file:bg-slate-800 cursor-pointer">

                        @error('images')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror

                        <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-600">
                            <span class="text-blue-600 font-bold">ℹ️ Info:</span>
                            <span>Foto pertama yang Anda pilih otomatis dijadikan sebagai <strong>Foto Utama</strong>. Setelah disimpan, Anda juga dapat mengelola atau menambah foto lain lewat menu Edit Lapangan.</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Status Operasional *</label>
                            <select name="status" required class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif (Dapat Dipesan)</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance (Pemeliharaan)</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
                            </select>
                        </div>

                        <div class="flex items-center pt-7">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
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
                            Simpan Lapangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
