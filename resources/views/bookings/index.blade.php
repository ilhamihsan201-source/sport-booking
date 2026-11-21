@extends('layouts.app')

@section('title', 'Jadwal & Reservasi Lapangan - SportBooking')

@section('content')
@php
    $today = now()->toDateString();
@endphp

{{-- NAVBAR --}}
<header class="sticky top-0 z-50 w-full border-b border-slate-800/60 bg-[#0B111E]/95 backdrop-blur-md">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center">
            <span class="text-2xl font-black tracking-tight text-white">
                SPORT<span class="text-[#CCFF00]">BOOKING</span>
            </span>
        </a>

        {{-- Navigation Links (Title Case, Medium Weight) --}}
        <nav class="hidden items-center gap-8 text-sm font-medium text-slate-300 lg:flex">
            <a href="{{ route('arena.index') }}" class="hover:text-white transition">Arena</a>
            <a href="{{ route('booking.index') }}" class="text-[#CCFF00] font-semibold transition">Jadwal</a>
            <a href="{{ route('home') }}#fasilitas" class="hover:text-white transition">Fasilitas</a>
            <a href="{{ route('home') }}#cara-pesan" class="hover:text-white transition">Cara Pesan</a>
        </nav>

        <div class="flex items-center gap-4">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="rounded-md border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                        Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('customer.dashboard') }}"
                       class="rounded-md border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                        Dashboard Saya
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="text-xs font-medium text-slate-400 hover:text-white transition px-2 py-1">
                    Masuk
                </a>
            @endauth

            <a href="{{ route('booking.index') }}"
               class="rounded-md bg-[#CCFF00] px-4 py-2 text-xs font-bold text-slate-950 transition hover:bg-[#b8e600] active:scale-95 shadow-xs">
                Pesan Sekarang
            </a>
        </div>
    </div>
</header>

<div class="min-h-screen bg-slate-50 text-slate-900">
    <section class="bg-[#071b49] text-white">
        <div class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
            <p class="mb-3 text-sm font-bold uppercase tracking-[0.25em] text-lime-300">
                Kinetic Court · Booking
            </p>
            <h1 class="max-w-2xl text-3xl font-black tracking-tight sm:text-5xl">
                Amankan waktu bermainmu.
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-blue-100 sm:text-base">
                Pilih lapangan, tentukan tanggal, lalu temukan jadwal yang tersedia.
                Semua jadwal diperbarui berdasarkan data reservasi.
            </p>
        </div>
    </section>

    {{-- VALIDATION ERROR ALERT --}}
    @if ($errors->any())
        <div class="mx-auto max-w-7xl px-5 pt-6 lg:px-8">
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
                <p class="font-bold">Terjadi kesalahan pada data pemesanan:</p>
                <ul class="mt-2 list-inside list-disc text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="mx-auto max-w-7xl px-5 py-9 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_350px]">

            {{-- PILIH JADWAL --}}
            <div class="space-y-6">

                {{-- SECTION 1: LAPANGAN --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-6 flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-lime-100 font-black text-[#071b49]">01</span>
                        <div>
                            <h2 class="text-lg font-extrabold">Pilih lapangan</h2>
                            <p class="text-sm text-slate-500">Tentukan arena yang ingin kamu gunakan.</p>
                        </div>
                    </div>

                    <label for="court_id" class="mb-2 block text-sm font-bold text-slate-700">
                        Lapangan tersedia
                    </label>

                    <select id="court_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <option value="">Pilih lapangan</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}"
                                {{ old('court_id', request('court_id')) == $court->id ? 'selected' : '' }}
                                data-name="{{ $court->name }}"
                                data-sport="{{ $court->sport->name ?? 'Olahraga' }}"
                                data-price="{{ $court->price_per_hour }}">
                                {{ $court->name }} — {{ $court->sport->name ?? 'Olahraga' }}
                            </option>
                        @endforeach
                    </select>
                </section>

                {{-- SECTION 2: TANGGAL & DURASI --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-6 flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-lime-100 font-black text-[#071b49]">02</span>
                        <div>
                            <h2 class="text-lg font-extrabold">Tentukan tanggal & durasi</h2>
                            <p class="text-sm text-slate-500">Pilih hari dan lama waktu bermain.</p>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="booking_date" class="mb-2 block text-sm font-bold text-slate-700">
                                Tanggal bermain
                            </label>
                            <input type="date" id="booking_date"
                                min="{{ $today }}"
                                value="{{ old('booking_date', request('date', $today)) }}"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="duration" class="mb-2 block text-sm font-bold text-slate-700">
                                Durasi bermain
                            </label>
                            <select id="duration"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                                <option value="1" {{ old('duration_hours') == '1' ? 'selected' : '' }}>1 jam</option>
                                <option value="2" {{ old('duration_hours') == '2' ? 'selected' : '' }}>2 jam</option>
                                <option value="3" {{ old('duration_hours') == '3' ? 'selected' : '' }}>3 jam</option>
                            </select>
                        </div>
                    </div>
                </section>

                {{-- SECTION 3: JAM BERMAIN --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-lime-100 font-black text-[#071b49]">03</span>
                            <div>
                                <h2 class="text-lg font-extrabold">Pilih jam bermain</h2>
                                <p id="slot-date-label" class="text-sm text-slate-500">Pilih lapangan untuk melihat jadwal.</p>
                            </div>
                        </div>

                        <button type="button" id="refresh-slots"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                            ↻ Perbarui
                        </button>
                    </div>

                    <div class="mb-5 flex flex-wrap gap-4 text-xs font-semibold text-slate-600">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded bg-lime-400"></span> Tersedia
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded bg-slate-300"></span> Sudah dipesan
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded bg-blue-700"></span> Dipilih
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded bg-slate-100 ring-1 ring-slate-200"></span> Lewat
                        </span>
                    </div>

                    <div id="slot-message"
                        class="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        Pilih lapangan untuk menampilkan jadwal yang tersedia.
                    </div>

                    <div id="slot-grid" class="hidden grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"></div>
                </section>

                {{-- SECTION 4: DATA PEMESAN --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-6 flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-lime-100 font-black text-[#071b49]">04</span>
                        <div>
                            <h2 class="text-lg font-extrabold">Informasi data pemesan</h2>
                            <p class="text-sm text-slate-500">Data untuk konfirmasi dan pengiriman kode tiket booking.</p>
                        </div>
                    </div>

                    <form id="booking-form" action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        {{-- Hidden inputs yang disinkronkan via JavaScript --}}
                        <input type="hidden" name="court_id" id="form-court-id" value="{{ old('court_id') }}">
                        <input type="hidden" name="booking_date" id="form-booking-date" value="{{ old('booking_date') }}">
                        <input type="hidden" name="start_time" id="form-start-time" value="{{ old('start_time') }}">
                        <input type="hidden" name="duration_hours" id="form-duration" value="{{ old('duration_hours', '1') }}">

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="customer_name" class="mb-2 block text-sm font-bold text-slate-700">
                                    Nama lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="customer_name" id="customer_name" required
                                    value="{{ old('customer_name', auth()->user()?->name) }}"
                                    placeholder="Contoh: Budi Santoso"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                                @error('customer_name')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_phone" class="mb-2 block text-sm font-bold text-slate-700">
                                    Nomor WhatsApp / HP <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="customer_phone" id="customer_phone" required
                                    value="{{ old('customer_phone') }}"
                                    placeholder="Contoh: 08123456789"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                                @error('customer_phone')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_email" class="mb-2 block text-sm font-bold text-slate-700">
                                    Email (Opsional)
                                </label>
                                <input type="email" name="customer_email" id="customer_email"
                                    value="{{ old('customer_email', auth()->user()?->email) }}"
                                    placeholder="Contoh: budi@gmail.com"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                                @error('customer_email')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="customer_notes" class="mb-2 block text-sm font-bold text-slate-700">
                                    Catatan tambahan (Opsional)
                                </label>
                                <textarea name="customer_notes" id="customer_notes" rows="2"
                                    placeholder="Misal: sewa raket tambahan atau rompi"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">{{ old('customer_notes') }}</textarea>
                            </div>
                        </div>
                    </form>
                </section>
            </div>

            {{-- RINGKASAN --}}
            <aside>
                <div class="sticky top-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 border-b border-slate-100 pb-5">
                        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-700">
                            Booking summary
                        </p>
                        <h2 class="mt-2 text-xl font-black text-slate-900">Ringkasan pesanan</h2>
                    </div>

                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-xs text-slate-500">Lapangan</p>
                            <p id="summary-court" class="mt-1 font-bold">Belum dipilih</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Olahraga</p>
                            <p id="summary-sport" class="mt-1 font-bold">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Tanggal</p>
                            <p id="summary-date" class="mt-1 font-bold">{{ now()->translatedFormat('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Jam bermain</p>
                            <p id="summary-time" class="mt-1 font-bold">Belum dipilih</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Durasi</p>
                            <p id="summary-duration" class="mt-1 font-bold">1 jam</p>
                        </div>
                    </div>

                    <div class="my-6 border-t border-dashed border-slate-200"></div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-semibold text-slate-500">Total harga</span>
                        <strong id="summary-total" class="text-xl font-black text-blue-800">Rp0</strong>
                    </div>

                    {{-- TOMBOL KONFIRMASI & PESAN --}}
                    <button type="submit" form="booking-form" id="submit-booking" disabled
                        class="mt-6 w-full cursor-not-allowed rounded-xl bg-slate-200 px-5 py-4 text-sm font-extrabold text-slate-500 transition duration-200">
                        Konfirmasi & Pesan
                    </button>

                    <p class="mt-3 text-center text-xs leading-5 text-slate-400">
                        Harga dihitung otomatis di server dan slot akan dilindungi selama 15 menit saat pembayaran.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const courtSelect = document.getElementById('court_id');
    const dateInput = document.getElementById('booking_date');
    const durationSelect = document.getElementById('duration');
    const slotGrid = document.getElementById('slot-grid');
    const slotMessage = document.getElementById('slot-message');
    const submitBtn = document.getElementById('submit-booking');
    const bookingForm = document.getElementById('booking-form');

    const formCourtId = document.getElementById('form-court-id');
    const formBookingDate = document.getElementById('form-booking-date');
    const formStartTime = document.getElementById('form-start-time');
    const formDuration = document.getElementById('form-duration');

    const summary = {
        court: document.getElementById('summary-court'),
        sport: document.getElementById('summary-sport'),
        date: document.getElementById('summary-date'),
        time: document.getElementById('summary-time'),
        duration: document.getElementById('summary-duration'),
        total: document.getElementById('summary-total'),
    };

    let selectedSlot = null;
    let currentSlots = [];

    const rupiah = value => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(value);

    const formatDate = value => {
        if (!value) return '—';
        return new Date(value + 'T00:00:00').toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    };

    function updateSummary() {
        const option = courtSelect.selectedOptions[0];

        summary.court.textContent = option?.dataset.name || 'Belum dipilih';
        summary.sport.textContent = option?.dataset.sport || '—';
        summary.date.textContent = formatDate(dateInput.value);
        summary.duration.textContent = durationSelect.value + ' jam';

        if (selectedSlot) {
            summary.time.textContent =
                selectedSlot.start_time + ' – ' + selectedSlot.end_time;
            summary.total.textContent = rupiah(selectedSlot.price);

            // Aktifkan tombol submit
            submitBtn.disabled = false;
            submitBtn.classList.remove('cursor-not-allowed', 'bg-slate-200', 'text-slate-500');
            submitBtn.classList.add('bg-lime-400', 'text-slate-950', 'hover:bg-lime-300', 'shadow-md', 'cursor-pointer');

            // Sinkronkan data form tersembunyi
            formCourtId.value = courtSelect.value;
            formBookingDate.value = dateInput.value;
            formStartTime.value = selectedSlot.start_time;
            formDuration.value = durationSelect.value;
        } else {
            summary.time.textContent = 'Belum dipilih';
            summary.total.textContent = rupiah(0);

            // Matikan tombol submit
            submitBtn.disabled = true;
            submitBtn.classList.add('cursor-not-allowed', 'bg-slate-200', 'text-slate-500');
            submitBtn.classList.remove('bg-lime-400', 'text-slate-950', 'hover:bg-lime-300', 'shadow-md', 'cursor-pointer');

            formStartTime.value = '';
        }
    }

    function renderSlots(slots) {
        slotGrid.innerHTML = '';
        currentSlots = slots;
        selectedSlot = null;
        updateSummary();

        if (!slots.length) {
            slotGrid.classList.add('hidden');
            slotMessage.classList.remove('hidden');
            slotMessage.textContent = 'Tidak ada jadwal yang tersedia pada tanggal ini.';
            return;
        }

        slotMessage.classList.add('hidden');
        slotGrid.classList.remove('hidden');

        slots.forEach(slot => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'rounded-xl border p-3 text-left transition';

            const isAvailable = slot.status === 'available';
            const isBooked = slot.status === 'booked';

            if (isAvailable) {
                button.classList.add(
                    'border-lime-300', 'bg-lime-50',
                    'hover:border-lime-500', 'hover:bg-lime-100', 'cursor-pointer'
                );
            } else if (isBooked) {
                button.classList.add('cursor-not-allowed', 'border-slate-200', 'bg-slate-100', 'opacity-60');
                button.disabled = true;
            } else {
                button.classList.add('cursor-not-allowed', 'border-slate-100', 'bg-slate-50', 'text-slate-400');
                button.disabled = true;
            }

            const statusLabel = isAvailable
                ? 'Tersedia'
                : (isBooked ? 'Sudah dipesan' : 'Lewat');

            button.innerHTML = `
                <span class="block text-sm font-extrabold">${slot.start_time} – ${slot.end_time}</span>
                <span class="mt-1 block text-[11px] font-semibold">${statusLabel}</span>
                <span class="mt-2 block text-xs font-bold">${rupiah(slot.price)}</span>
            `;

            if (isAvailable) {
                button.addEventListener('click', () => {
                    slotGrid.querySelectorAll('button').forEach(item => {
                        item.classList.remove('border-blue-700', 'bg-blue-700', 'text-white');
                        item.classList.add('border-lime-300', 'bg-lime-50');
                    });

                    button.classList.remove('border-lime-300', 'bg-lime-50');
                    button.classList.add('border-blue-700', 'bg-blue-700', 'text-white');

                    selectedSlot = slot;
                    updateSummary();
                });
            }

            slotGrid.appendChild(button);
        });
    }

    async function loadAvailability() {
        const courtId = courtSelect.value;
        const date = dateInput.value;
        const duration = durationSelect.value;

        selectedSlot = null;
        updateSummary();

        if (!courtId) {
            slotGrid.classList.add('hidden');
            slotMessage.classList.remove('hidden');
            slotMessage.textContent = 'Pilih lapangan untuk menampilkan jadwal yang tersedia.';
            return;
        }

        if (!date) {
            slotGrid.classList.add('hidden');
            slotMessage.classList.remove('hidden');
            slotMessage.textContent = 'Silakan pilih tanggal bermain.';
            return;
        }

        slotGrid.classList.add('hidden');
        slotMessage.classList.remove('hidden');
        slotMessage.textContent = 'Memuat jadwal...';

        document.getElementById('slot-date-label').textContent = formatDate(date);

        const url = new URL(@json(route('booking.availability', [], false)), window.location.origin);
        url.searchParams.set('court_id', courtId);
        url.searchParams.set('date', date);
        url.searchParams.set('duration', duration);

        try {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal mengambil jadwal.');
            }

            renderSlots(data.slots || []);
        } catch (error) {
            slotGrid.classList.add('hidden');
            slotMessage.classList.remove('hidden');
            slotMessage.textContent = error.message || 'Terjadi kesalahan saat memuat jadwal.';
        }
    }

    bookingForm.addEventListener('submit', (e) => {
        if (!selectedSlot) {
            e.preventDefault();
            alert('Silakan pilih salah satu jam bermain yang tersedia terlebih dahulu.');
            return;
        }

        formCourtId.value = courtSelect.value;
        formBookingDate.value = dateInput.value;
        formDuration.value = durationSelect.value;
        formStartTime.value = selectedSlot.start_time;
    });

    courtSelect.addEventListener('change', loadAvailability);
    dateInput.addEventListener('change', loadAvailability);
    durationSelect.addEventListener('change', loadAvailability);
    document.getElementById('refresh-slots').addEventListener('click', loadAvailability);

    updateSummary();
    if (courtSelect.value) {
        loadAvailability();
    }
});
</script>
@endsection
