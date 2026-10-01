@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.bookings_title'))
@section('page-title', __('rental-hub::rental.bookings_title'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ __('rental-hub::rental.bookings_title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('rental-hub::rental.bookings_subtitle') }}</p>
        </div>
        <div>
            <a href="{{ route('rental.bookings.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm transition-colors min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ __('rental-hub::rental.quick_rent_cta') }}</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('rental.bookings.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4">
            <div class="sm:col-span-6">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}"
                       placeholder="{{ $isAdmin ? 'Cari kode, nama armada, plat, atau pelanggan...' : 'Cari kode booking atau nama armada...' }}" 
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm min-h-[42px]">
            </div>

            <div class="sm:col-span-4">
                <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm min-h-[42px]">
                    <option value="">Semua Status Reservasi</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" {{ ($currentStatus ?? '') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold shadow-sm transition-colors min-h-[42px]">
                    {{ __('rental-hub::rental.common_filter') }}
                </button>
                @if (!empty($search) || !empty($currentStatus))
                    <a href="{{ route('rental.bookings.index') }}" 
                       title="{{ __('rental-hub::rental.common_reset') }}"
                       class="inline-flex items-center justify-center p-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 min-h-[42px] min-w-[42px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">{{ __('rental-hub::rental.booking_code') }}</th>
                        @if ($isAdmin)
                            <th class="px-6 py-3.5 text-left">{{ __('rental-hub::rental.booking_customer') }}</th>
                        @endif
                        <th class="px-6 py-3.5 text-left">{{ __('rental-hub::rental.booking_unit') }}</th>
                        <th class="px-6 py-3.5 text-left">Rentang Sewa</th>
                        <th class="px-6 py-3.5 text-left">{{ __('rental-hub::rental.booking_total_price') }}</th>
                        <th class="px-6 py-3.5 text-left">{{ __('rental-hub::rental.common_status') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('rental-hub::rental.common_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bookings as $b)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $b->booking_code }}
                            </td>
                            @if ($isAdmin)
                                <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                                    <div class="font-medium text-slate-900">{{ $b->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-400">{{ $b->user->phone ?? $b->user->email ?? '' }}</div>
                                </td>
                            @endif
                            <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                                <div class="font-semibold text-slate-900">{{ $b->unit->name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-400">{{ $b->unit->code ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 whitespace-nowrap text-xs">
                                <div>{{ $b->formatted_start_time }}</div>
                                <div class="text-slate-400">s/d {{ $b->formatted_end_time }}</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                                {{ $b->formatted_total_price }}
                                @if ($b->late_fee > 0)
                                    <span class="block text-[11px] font-normal text-rose-600">+ denda {{ $b->formatted_late_fee }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $b->status->badgeClass() }}">
                                    {{ $b->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('rental.bookings.show', $b) }}" 
                                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 p-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                                    {{ __('rental-hub::rental.common_details') }}
                                </a>

                                @can('cancel', $b)
                                    <form method="POST" action="{{ route('rental.bookings.cancel', $b) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 p-1.5 rounded-lg hover:bg-rose-50 transition-colors">
                                            {{ __('rental-hub::rental.common_cancel') }}
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 7 : 6 }}" class="px-6 py-12 text-center text-slate-400">
                                {{ __('rental-hub::rental.common_no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
