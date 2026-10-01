@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.units_title'))
@section('page-title', __('rental-hub::rental.units_title'))

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ __('rental-hub::rental.units_title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('rental-hub::rental.units_subtitle') }}</p>
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

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('rental.units.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4">
            <div class="sm:col-span-5">
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama armada atau nomor plat..." 
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm min-h-[42px]">
            </div>

            <div class="sm:col-span-3">
                <select name="category_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm min-h-[42px]">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm min-h-[42px]">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold shadow-sm transition-colors min-h-[42px]">
                    {{ __('rental-hub::rental.common_filter') }}
                </button>
                @if (!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['status']))
                    <a href="{{ route('rental.units.index') }}" 
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

    <!-- Units Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($units as $unit)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <!-- Photo Header -->
                    <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                        <img src="{{ $unit->photo_url }}" 
                             alt="{{ $unit->name }}" 
                             class="w-full h-full object-cover">
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm {{ $unit->status->badgeClass() }}">
                                {{ $unit->status->label() }}
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
                        <div class="text-xs font-semibold text-indigo-600 mb-1">
                            {{ $unit->category->name ?? 'Tanpa Kategori' }}
                        </div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $unit->name }}</h3>

                        <!-- Specifications Preview -->
                        @if (!empty($unit->specifications))
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach (array_slice($unit->specifications, 0, 3) as $k => $v)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] bg-slate-100 text-slate-600">
                                        {{ $k }}: {{ $v }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Tariffs -->
                        <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
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

                <!-- Footer Actions -->
                <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('rental.units.edit', $unit) }}" 
                       class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 p-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                        {{ __('rental-hub::rental.common_edit') }}
                    </a>

                    <form method="POST" action="{{ route('rental.units.destroy', $unit) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 p-1.5 rounded-lg hover:bg-rose-50 transition-colors">
                            {{ __('rental-hub::rental.common_delete') }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.common_no_data') }}</p>
                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $units->links() }}
    </div>
</div>
@endsection
