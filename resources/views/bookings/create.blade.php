@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.booking_create_title'))
@section('page-title', __('rental-hub::rental.booking_create_title'))

@section('content')
@php
    $unitsData = $units->map(function ($u) {
        return [
            'id' => $u->id,
            'name' => $u->name,
            'code' => $u->code,
            'category' => $u->category->name ?? 'Armada',
            'price_per_hour' => (int) $u->price_per_hour,
            'price_per_day' => (int) $u->price_per_day,
            'photo_url' => $u->photo_url,
        ];
    });
@endphp

<div class="max-w-4xl space-y-6" 
     x-data="{
         units: {{ json_encode($unitsData) }},
         selectedUnitId: '{{ old('unit_id', $selectedUnitId ?? ($units->first()->id ?? '')) }}',
         startTime: '{{ old('start_time', now()->addHour()->format('Y-m-d\TH:00')) }}',
         endTime: '{{ old('end_time', now()->addDays(1)->addHour()->format('Y-m-d\TH:00')) }}',
         
         get selectedUnit() {
             return this.units.find(u => u.id == this.selectedUnitId) || null;
         },
         
         get calculation() {
             if (!this.selectedUnit || !this.startTime || !this.endTime) {
                 return { hours: 0, days: 0, remHours: 0, totalPrice: 0, valid: false };
             }
             
             let start = new Date(this.startTime);
             let end = new Date(this.endTime);
             let diffMs = end - start;
             
             if (diffMs <= 0) {
                 return { hours: 0, days: 0, remHours: 0, totalPrice: 0, valid: false };
             }
             
             let totalHours = Math.ceil(diffMs / (1000 * 60 * 60));
             if (totalHours <= 0) totalHours = 1;
             
             let days = Math.floor(totalHours / 24);
             let remHours = totalHours % 24;
             let total = 0;
             
             if (totalHours >= 24) {
                 total = (days * this.selectedUnit.price_per_day) + (remHours * this.selectedUnit.price_per_hour);
             } else {
                 total = totalHours * this.selectedUnit.price_per_hour;
             }
             
             return {
                 hours: totalHours,
                 days: days,
                 remHours: remHours,
                 totalPrice: total,
                 valid: true
             };
         },
         
         formatRupiah(num) {
             return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
         }
     }">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ __('rental-hub::rental.booking_create_title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('rental-hub::rental.booking_create_subtitle') }}</p>
        </div>
        <a href="{{ route('rental.bookings.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors min-h-[44px]">
            &larr; {{ __('rental-hub::rental.common_back') }}
        </a>
    </div>

    @if ($units->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <p class="text-base font-bold text-slate-800">Saat ini tidak ada armada yang berstatus tersedia.</p>
            <p class="text-xs text-slate-500 mt-1">Silakan coba beberapa saat lagi atau hubungi pihak pengelola rental.</p>
        </div>
    @else
        <form method="POST" action="{{ route('rental.bookings.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @csrf

            <!-- Left Form Section -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <!-- Unit Selector -->
                <div>
                    <label for="unit_id" class="block text-sm font-semibold text-slate-700">Pilih Armada Kendaraan *</label>
                    <select id="unit_id" 
                            name="unit_id" 
                            x-model="selectedUnitId"
                            required 
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                        @foreach ($units as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->name }} ({{ $u->code }}) - {{ $u->category->name ?? 'Mobil' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Schedule Section -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <label for="start_time" class="block text-sm font-semibold text-slate-700">Waktu Mulai Sewa *</label>
                        <input type="datetime-local" 
                               id="start_time" 
                               name="start_time" 
                               x-model="startTime"
                               required 
                               class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-semibold text-slate-700">Waktu Selesai Sewa *</label>
                        <input type="datetime-local" 
                               id="end_time" 
                               name="end_time" 
                               x-model="endTime"
                               required 
                               class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="pt-4 border-t border-slate-100">
                    <label for="notes" class="block text-sm font-semibold text-slate-700">Catatan Khusus (Opsional)</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3" 
                              placeholder="Tujuan perjalanan, permintaan tambahan perlengkapan armada, dsb."
                              class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400">* Wajib diisi dengan jadwal akurat</span>
                    <button type="submit" 
                            :disabled="!calculation.valid"
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition-all min-h-[44px]">
                        Konfirmasi & Ajukan Booking
                    </button>
                </div>
            </div>

            <!-- Right Preview & Pricing Card -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                    <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Ringkasan Estimasi Biaya</h3>

                    <template x-if="selectedUnit">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <img :src="selectedUnit.photo_url" 
                                     :alt="selectedUnit.name" 
                                     class="w-20 h-14 object-cover rounded-xl bg-slate-100 border border-slate-100">
                                <div>
                                    <div class="text-xs font-semibold text-indigo-600" x-text="selectedUnit.category"></div>
                                    <div class="text-sm font-bold text-slate-900" x-text="selectedUnit.name"></div>
                                    <div class="text-xs font-mono text-slate-500" x-text="selectedUnit.code"></div>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-xl p-3.5 space-y-2 text-xs text-slate-600">
                                <div class="flex justify-between">
                                    <span>Tarif per Jam:</span>
                                    <span class="font-semibold text-slate-800" x-text="formatRupiah(selectedUnit.price_per_hour)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tarif per Hari (24 Jam):</span>
                                    <span class="font-semibold text-slate-800" x-text="formatRupiah(selectedUnit.price_per_day)"></span>
                                </div>
                            </div>

                            <div class="space-y-2.5 text-xs text-slate-600 pt-2 border-t border-slate-100">
                                <div class="flex justify-between">
                                    <span>Durasi Dihitung:</span>
                                    <span class="font-bold text-slate-900" x-text="calculation.hours + ' Jam (' + calculation.days + ' Hari ' + calculation.remHours + ' Jam)'"></span>
                                </div>
                                <div class="flex justify-between text-slate-500 text-[11px]">
                                    <span>Skema:</span>
                                    <span x-text="calculation.hours >= 24 ? 'Tarif harian + tarif sisa jam' : 'Tarif akumulasi per jam'"></span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                                <span class="text-sm font-bold text-slate-900">Total Biaya Sewa:</span>
                                <span class="text-lg font-bold text-indigo-600" x-text="formatRupiah(calculation.totalPrice)"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
