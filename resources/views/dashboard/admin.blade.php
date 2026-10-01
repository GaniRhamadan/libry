@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.dashboard_admin_title'))
@section('page-title', __('rental-hub::rental.nav_dashboard'))

@section('content')
<div class="space-y-8">
    <!-- Header Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ __('rental-hub::rental.dashboard_admin_title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('rental-hub::rental.dashboard_admin_subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('rental.units.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm transition-colors min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ __('rental-hub::rental.unit_add') }}</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Units -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rental-hub::rental.metric_total_units') }}</div>
            <div class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ number_format($stats['total_units']) }}</div>
            <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                <span class="inline-block w-2 h-2 rounded-full bg-slate-400"></span>
                <span>Unit terdaftar</span>
            </div>
        </div>

        <!-- Available Units -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rental-hub::rental.metric_available_units') }}</div>
            <div class="mt-2 text-2xl sm:text-3xl font-bold text-emerald-600">{{ number_format($stats['available_units']) }}</div>
            <div class="mt-3 flex items-center gap-2 text-xs text-emerald-700">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Siap disewa</span>
            </div>
        </div>

        <!-- Rented Units -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rental-hub::rental.metric_rented_units') }}</div>
            <div class="mt-2 text-2xl sm:text-3xl font-bold text-indigo-600">{{ number_format($stats['rented_units']) }}</div>
            <div class="mt-3 flex items-center gap-2 text-xs text-indigo-700">
                <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
                <span>Di tangan pelanggan</span>
            </div>
        </div>

        <!-- Maintenance Units -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rental-hub::rental.metric_maintenance_units') }}</div>
            <div class="mt-2 text-2xl sm:text-3xl font-bold text-amber-600">{{ number_format($stats['maintenance_units']) }}</div>
            <div class="mt-3 flex items-center gap-2 text-xs text-amber-700">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Bengkel / Servis</span>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rental-hub::rental.metric_pending_bookings') }}</div>
            <div class="mt-2 text-2xl sm:text-3xl font-bold text-amber-600">{{ number_format($stats['pending_bookings']) }}</div>
            <div class="mt-3 text-xs text-slate-500">
                <a href="{{ route('rental.bookings.index', ['status' => 'pending']) }}" class="text-indigo-600 font-semibold hover:underline">Tinjau permohonan</a>
            </div>
        </div>

        <!-- Active Bookings -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('rental-hub::rental.metric_active_bookings') }}</div>
            <div class="mt-2 text-2xl sm:text-3xl font-bold text-indigo-600">{{ number_format($stats['active_bookings']) }}</div>
            <div class="mt-3 text-xs text-slate-500">
                <a href="{{ route('rental.bookings.index', ['status' => 'active']) }}" class="text-indigo-600 font-semibold hover:underline">Pantau perjalanan</a>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-span-2 bg-gradient-to-br from-slate-900 to-slate-800 p-5 sm:p-6 rounded-2xl text-white shadow-sm">
            <div class="text-xs font-semibold text-slate-300 uppercase tracking-wider">{{ __('rental-hub::rental.metric_monthly_revenue') }}</div>
            <div class="mt-2 text-2xl sm:text-3xl font-bold text-emerald-400">
                {{ config('rental-hub.currency_symbol', 'Rp ') }}{{ number_format($stats['revenue_this_month'], 0, ',', '.') }}
            </div>
            <div class="mt-3 text-xs text-slate-400">Total pendapatan sewa yang telah selesai bulan ini.</div>
        </div>
    </div>

    <!-- Overdue Bookings Alert Section (If Any) -->
    @if ($overdueBookings->isNotEmpty())
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 sm:p-6">
            <div class="flex items-center gap-3 text-rose-800 font-bold text-base mb-3">
                <svg class="w-6 h-6 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ __('rental-hub::rental.overdue_alert_title') }} ({{ $overdueBookings->count() }})</span>
            </div>
            <p class="text-sm text-rose-700 mb-4">{{ __('rental-hub::rental.overdue_alert_desc') }}</p>

            <div class="overflow-x-auto rounded-xl border border-rose-200 bg-white">
                <table class="min-w-full divide-y divide-rose-100 text-sm">
                    <thead class="bg-rose-100/50 text-rose-900 font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Kode</th>
                            <th class="px-4 py-3 text-left">Pelanggan</th>
                            <th class="px-4 py-3 text-left">Armada</th>
                            <th class="px-4 py-3 text-left">Batas Kembali</th>
                            <th class="px-4 py-3 text-left">Keterlambatan</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-100">
                        @foreach ($overdueBookings as $overdue)
                            <tr class="hover:bg-rose-50/40">
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $overdue->booking_code }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $overdue->user->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $overdue->unit->name ?? 'N/A' }} ({{ $overdue->unit->code ?? '' }})</td>
                                <td class="px-4 py-3 text-slate-600">{{ $overdue->formatted_end_time }}</td>
                                <td class="px-4 py-3 font-semibold text-rose-600">{{ $overdue->overdue_hours }} Jam</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('rental.bookings.show', $overdue) }}" 
                                       class="inline-flex items-center px-3 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700 transition-colors">
                                        Proses Pengembalian
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- 5 Latest Bookings -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">{{ __('rental-hub::rental.latest_bookings_title') }}</h2>
            <a href="{{ route('rental.bookings.index') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">Kode</th>
                        <th class="px-6 py-3.5 text-left">Pelanggan</th>
                        <th class="px-6 py-3.5 text-left">Armada</th>
                        <th class="px-6 py-3.5 text-left">Jadwal Sewa</th>
                        <th class="px-6 py-3.5 text-left">Total Tarif</th>
                        <th class="px-6 py-3.5 text-left">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($latestBookings as $booking)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                {{ $booking->booking_code }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                                {{ $booking->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                                {{ $booking->unit->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap text-xs">
                                <div>{{ $booking->formatted_start_time }}</div>
                                <div class="text-slate-400">s/d {{ $booking->formatted_end_time }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                {{ $booking->formatted_total_price }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $booking->status->badgeClass() }}">
                                    {{ $booking->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('rental.bookings.show', $booking) }}" 
                                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50 transition-colors">
                                    {{ __('rental-hub::rental.common_details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                {{ __('rental-hub::rental.common_no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
