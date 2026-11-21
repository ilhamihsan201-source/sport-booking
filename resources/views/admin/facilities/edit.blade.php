<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">SportBooking Admin</p>
                <h2 class="text-2xl font-black text-slate-900">Edit Fasilitas: {{ $facility->name }}</h2>
            </div>
            <a href="{{ route('admin.facilities.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
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

                <form method="POST" action="{{ route('admin.facilities.update', $facility) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Nama Fasilitas *</label>
                        <input type="text" name="name" value="{{ old('name', $facility->name) }}" required
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $facility->description) }}</textarea>
                    </div>

                    <div class="border-t border-slate-100 pt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.facilities.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="rounded-xl bg-[#0B1F4D] px-6 py-2.5 text-sm font-bold text-[#CCFF00] hover:bg-slate-800 transition">
                            Perbarui Fasilitas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
