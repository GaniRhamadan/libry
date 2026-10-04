@php
    $isAdmin = (auth()->user()->rental_role ?? 'customer') === 'admin';
    $currentRoute = request()->route()?->getName() ?? '';
@endphp

<nav class="space-y-5">
    @if ($isAdmin)
        <!-- Admin Management Section -->
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Panel Administrator
            </div>
            <div class="space-y-1">
                <!-- Dashboard Admin -->
                <a href="{{ route('rental.dashboard') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ str_starts_with($currentRoute, 'rental.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ str_starts_with($currentRoute, 'rental.dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_dashboard') }}</span>
                </a>

                <!-- Kelola Armada (Units) -->
                <a href="{{ route('rental.units.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ str_starts_with($currentRoute, 'rental.units') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ str_starts_with($currentRoute, 'rental.units') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_units') }}</span>
                </a>

                <!-- Kelola Kategori -->
                <a href="{{ route('rental.categories.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ str_starts_with($currentRoute, 'rental.categories') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ str_starts_with($currentRoute, 'rental.categories') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_categories') }}</span>
                </a>

                <!-- Kelola Booking Pelanggan -->
                <a href="{{ route('rental.bookings.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ str_starts_with($currentRoute, 'rental.bookings') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ str_starts_with($currentRoute, 'rental.bookings') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_admin_bookings') }}</span>
                </a>
            </div>
        </div>
    @else
        <!-- Customer Rental Section -->
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Menu Penyewa
            </div>
            <div class="space-y-1">
                <!-- Dashboard Penyewa -->
                <a href="{{ route('rental.dashboard') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ $currentRoute === 'rental.dashboard' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ $currentRoute === 'rental.dashboard' ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_dashboard') }}</span>
                </a>

                <!-- Katalog Armada Siap Sewa -->
                <a href="{{ route('rental.catalog') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ str_starts_with($currentRoute, 'rental.catalog') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ str_starts_with($currentRoute, 'rental.catalog') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_catalog') }}</span>
                </a>

                <!-- Sewa Kendaraan (Form Reservasi) -->
                <a href="{{ route('rental.bookings.create') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ $currentRoute === 'rental.bookings.create' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ $currentRoute === 'rental.bookings.create' ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_rent_now') }}</span>
                </a>

                <!-- Sewa Saya (Riwayat & Reservasi) -->
                <a href="{{ route('rental.bookings.index') }}" 
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ str_starts_with($currentRoute, 'rental.bookings') && $currentRoute !== 'rental.bookings.create' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ str_starts_with($currentRoute, 'rental.bookings') && $currentRoute !== 'rental.bookings.create' ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_my_bookings') }}</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Profile & Account Section -->
    <div class="pt-3 border-t border-slate-100">
        <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
            Akun
        </div>
        <a href="{{ route('rental.profile.edit') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl transition-colors {{ str_starts_with($currentRoute, 'rental.profile') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ str_starts_with($currentRoute, 'rental.profile') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>{{ __('rental-hub::rental.nav_profile') }}</span>
        </a>
    </div>
</nav>
