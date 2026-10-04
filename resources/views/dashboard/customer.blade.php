@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.dashboard_customer_title'))
@section('page-title', __('rental-hub::rental.nav_dashboard'))

@section('content')
<div class="space-y-8">
    <!-- Hero / Quick Rent Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-indigo-600 p-6 sm:p-8 text-white shadow-sm">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur text-white mb-3">
                🚗 Layanan Sewa Kendaraan Terpercaya
            </span>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Halo, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-sm text-indigo-100 leading-relaxed">
                Butuh kendaraan untuk operasional perjalanan, mudik, atau liburan keluarga? Jelajahi armada pilihan kami dengan tarif transparan dan proses sewa instan.
            </p>
            <div class="mt-5 flex flex-wrap items-center gap-3">
                <a href="{{ route('rental.catalog') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-indigo-600 font-bold text-sm shadow hover:bg-indigo-50 transition-colors min-h-[44px]">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_catalog') }}</span>
                </a>

                <a href="{{ route('rental.bookings.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-700/80 hover:bg-indigo-700 text-white font-semibold text-sm transition-colors min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>{{ __('rental-hub::rental.nav_rent_now') }}</span>
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none hidden sm:block">
            <svg class="w-72 h-72 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
            </svg>
        </div>
    </div>

    <!-- Available Fleet Preview Section -->
    @if (isset($availableUnits) && $availableUnits->isNotEmpty())
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Armada Siap Sewa Hari Ini</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilihan armada kendaraan dengan performa prima siap langsung digunakan.</p>
                </div>
                <a href="{{ route('rental.catalog') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    Lihat Semua di Katalog &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ($availableUnits as $unit)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <div class="relative h-40 w-full bg-slate-100 overflow-hidden">
                                <img src="{{ $unit->photo_url }}" 
                                     alt="{{ $unit->name }}" 
                                     class="w-full h-full object-cover">
                                <div class="absolute top-2.5 right-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500 text-white shadow-sm">
                                        Tersedia
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider mb-0.5">
                                    {{ $unit->category->name ?? 'Armada' }}
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $unit->name }}</h3>
                                <div class="text-xs font-mono text-slate-400 mt-0.5">{{ $unit->code }}</div>

                                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Tarif per Hari:</span>
                                    <span class="font-bold text-slate-900">{{ $unit->formatted_price_per_day }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 pt-0">
                            <a href="{{ route('rental.bookings.create', ['unit_id' => $unit->id]) }}" 
                               class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white text-xs font-bold transition-colors">
                                <span>Sewa Armada Ini</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Active Reservations Section -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900">{{ __('rental-hub::rental.active_reservations') }}</h2>

        @if ($activeBookings->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700">Tidak ada reservasi yang sedang berjalan</p>
                <p class="text-xs text-slate-500 mt-1">Pilih armada favorit Anda di katalog dan ajukan sewa kapan saja.</p>
                <div class="mt-4">
                    <a href="{{ route('rental.catalog') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">
                        Buka Katalog Armada &rarr;
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($activeBookings as $booking)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                        <div class="p-5">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-mono font-bold text-slate-500">{{ $booking->booking_code }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $booking->status->badgeClass() }}">
                                    {{ $booking->status->label() }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 mb-4">
                                <img src="{{ $booking->unit->photo_url }}" 
                                     alt="{{ $booking->unit->name }}" 
                                     class="w-16 h-12 object-cover rounded-lg bg-slate-100 flex-shrink-0">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 leading-tight">{{ $booking->unit->name }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $booking->unit->code }}</p>
                                </div>
                            </div>

                            <div class="space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Mulai:</span>
                                    <span class="font-medium text-slate-800">{{ $booking->formatted_start_time }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Selesai:</span>
                                    <span class="font-medium text-slate-800">{{ $booking->formatted_end_time }}</span>
                                </div>
                                <div class="flex justify-between pt-1 border-t border-slate-200">
                                    <span class="text-slate-500">Total Tarif:</span>
                                    <span class="font-bold text-slate-900">{{ $booking->formatted_total_price }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-3 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('rental.bookings.show', $booking) }}" 
                               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                {{ __('rental-hub::rental.common_details') }} &rarr;
                            </a>

                            @if ($booking->status->isCancelable())
                                <form method="POST" action="{{ route('rental.bookings.cancel', $booking) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                        {{ __('rental-hub::rental.common_cancel') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Booking History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">{{ __('rental-hub::rental.booking_history') }}</h2>
            <a href="{{ route('rental.bookings.index') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                Semua Riwayat &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">Kode</th>
                        <th class="px-6 py-3.5 text-left">Armada</th>
                        <th class="px-6 py-3.5 text-left">Jadwal Sewa</th>
                        <th class="px-6 py-3.5 text-left">Total Pembayaran</th>
                        <th class="px-6 py-3.5 text-left">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentHistory as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                {{ $item->booking_code }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                                {{ $item->unit->name }} ({{ $item->unit->code }})
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap text-xs">
                                <div>{{ $item->formatted_start_time }}</div>
                                <div class="text-slate-400">s/d {{ $item->formatted_end_time }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                {{ $item->formatted_total_price }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->status->badgeClass() }}">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('rental.bookings.show', $item) }}" 
                                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                    {{ __('rental-hub::rental.common_details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Belum ada riwayat pesanan sewa sebelumnya.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
