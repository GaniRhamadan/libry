@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.catalog_title'))
@section('page-title', __('rental-hub::rental.nav_catalog'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ __('rental-hub::rental.catalog_title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('rental-hub::rental.catalog_subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('rental.bookings.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors min-h-[44px]">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>{{ __('rental-hub::rental.nav_my_bookings') }}</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('rental.catalog') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4">
            <div class="sm:col-span-6">
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama kendaraan atau tipe..." 
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm min-h-[42px]">
            </div>

            <div class="sm:col-span-4">
                <select name="category_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm min-h-[42px]">
                    <option value="">Semua Kategori Kendaraan</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-colors min-h-[42px]">
                    {{ __('rental-hub::rental.common_filter') }}
                </button>
                @if (!empty($filters['search']) || !empty($filters['category_id']))
                    <a href="{{ route('rental.catalog') }}" 
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

    <!-- Units Catalog Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($units as $unit)
            <div class="group bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md hover:border-indigo-200 transition-all">
                <div>
                    <!-- Photo Header -->
                    <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                        <img src="{{ $unit->photo_url }}" 
                             alt="{{ $unit->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm bg-emerald-500 text-white">
                                {{ __('rental-hub::rental.unit_status_available') }}
                            </span>
                        </div>
                        <div class="absolute bottom-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-900/80 backdrop-blur text-white font-mono">
                                {{ $unit->code }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">
                            {{ $unit->category->name ?? 'Mobil' }}
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 leading-snug group-hover:text-indigo-600 transition-colors">
                            {{ $unit->name }}
                        </h2>

                        <!-- Specifications Preview -->
                        @if (!empty($unit->specifications))
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach (array_slice($unit->specifications, 0, 4) as $k => $v)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs bg-slate-100 text-slate-700 font-medium">
                                        {{ $k }}: {{ $v }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Tariffs Breakdown -->
                        <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-2 gap-3 text-xs bg-slate-50/70 p-3 rounded-xl">
                            <div>
                                <span class="text-slate-500 block">Tarif per Jam:</span>
                                <span class="font-bold text-slate-800 text-sm">{{ $unit->formatted_price_per_hour }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Tarif per Hari:</span>
                                <span class="font-bold text-indigo-600 text-sm">{{ $unit->formatted_price_per_day }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="p-5 pt-0">
                    <a href="{{ route('rental.bookings.create', ['unit_id' => $unit->id]) }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm hover:shadow transition-all min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>{{ __('rental-hub::rental.catalog_rent_unit') }}</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-base font-bold text-slate-800">{{ __('rental-hub::rental.catalog_empty') }}</p>
                <p class="text-xs text-slate-500 mt-1">Coba atur ulang filter kategori atau kata kunci pencarian Anda.</p>
                <div class="mt-4">
                    <a href="{{ route('rental.catalog') }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                        Reset Pencarian &rarr;
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($units->hasPages())
        <div class="mt-6 bg-white p-4 rounded-2xl border border-slate-200">
            {{ $units->links() }}
        </div>
    @endif
</div>
@endsection
