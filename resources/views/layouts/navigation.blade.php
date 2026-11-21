<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()?->isAdmin() ? route('admin.dashboard') : route('home') }}" class="flex items-center gap-2 group">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#0B1F4D] text-sm font-black text-[#CCFF00] shadow-sm transition group-hover:scale-105">
                            SB
                        </div>
                        <span class="text-base font-black tracking-tight text-slate-900">
                            Sport<span class="text-blue-600">Booking</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-8 sm:flex">
                    @if(Auth::user()?->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Dashboard
                        </x-nav-link>

                        <x-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.*')">
                            Booking
                        </x-nav-link>

                        <x-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                            Pembayaran
                        </x-nav-link>

                        <x-nav-link :href="route('admin.courts.index')" :active="request()->routeIs('admin.courts.*')">
                            Lapangan
                        </x-nav-link>

                        <x-nav-link :href="route('admin.venues.index')" :active="request()->routeIs('admin.venues.*')">
                            Venue
                        </x-nav-link>

                        <x-nav-link :href="route('admin.facilities.index')" :active="request()->routeIs('admin.facilities.*')">
                            Fasilitas
                        </x-nav-link>

                        <x-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">
                            Pelanggan
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')">
                            Dashboard Saya
                        </x-nav-link>

                        <x-nav-link :href="route('booking.index')" :active="request()->routeIs('booking.*')">
                            Pesan Lapangan
                        </x-nav-link>

                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            Beranda
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="mr-3">
                    @if(Auth::user()?->isAdmin())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200">
                            Admin
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                            Member
                        </span>
                    @endif
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-slate-200 text-sm leading-4 font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1.5 text-slate-400">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-xs text-slate-400">Masuk sebagai</p>
                            <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Pengaturan Profil') }}
                        </x-dropdown-link>

                        @if(Auth::user()?->isAdmin())
                            <x-dropdown-link :href="route('home')">
                                Ke Halaman Utama
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50">
                                {{ __('Keluar (Log Out)') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-50 border-t border-slate-100">
        <div class="pt-2 pb-3 space-y-1 px-3">
            @if(Auth::user()?->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Dashboard Admin
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.*')">
                    Kelola Booking
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                    Kelola Pembayaran
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.courts.index')" :active="request()->routeIs('admin.courts.*')">
                    Kelola Lapangan
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.venues.index')" :active="request()->routeIs('admin.venues.*')">
                    Kelola Venue
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.facilities.index')" :active="request()->routeIs('admin.facilities.*')">
                    Kelola Fasilitas
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">
                    Kelola Pelanggan
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')">
                    Dashboard Saya
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('booking.index')" :active="request()->routeIs('booking.*')">
                    Pesan Lapangan
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    Halaman Utama
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-2 border-t border-slate-200 px-4 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-bold text-sm text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>
                <span class="rounded px-2 py-0.5 text-[10px] font-black uppercase {{ Auth::user()->isAdmin() ? 'bg-blue-100 text-blue-900' : 'bg-slate-100 text-slate-700' }}">
                    {{ Auth::user()->role }}
                </span>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Pengaturan Profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-red-600">
                        {{ __('Keluar (Log Out)') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
