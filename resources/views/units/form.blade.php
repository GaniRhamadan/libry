@extends('rental-hub::layout')

@section('title', $isEdit ? __('rental-hub::rental.unit_edit') : __('rental-hub::rental.unit_add'))
@section('page-title', $isEdit ? __('rental-hub::rental.unit_edit') : __('rental-hub::rental.unit_add'))

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                {{ $isEdit ? __('rental-hub::rental.unit_edit') : __('rental-hub::rental.unit_add') }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi parameter teknis armada, foto, dan tarif sewa kendaraan.</p>
        </div>
        <a href="{{ route('rental.units.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors min-h-[44px]">
            &larr; {{ __('rental-hub::rental.common_back') }}
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form method="POST" 
              action="{{ $isEdit ? route('rental.units.update', $unit) : route('rental.units.store') }}" 
              enctype="multipart/form-data" 
              class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <!-- Basic Info Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.unit_name') }} *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           value="{{ old('name', $unit->name) }}"
                           placeholder="Contoh: Toyota Avanza 1.3 G"
                           class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                </div>

                <div>
                    <label for="code" class="block text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.unit_code') }} *</label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           required 
                           value="{{ old('code', $unit->code) }}"
                           placeholder="Contoh: B 1234 RNT"
                           class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-mono text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.unit_category') }} *</label>
                    <select id="category_id" 
                            name="category_id" 
                            required 
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                        <option value="">Pilih Kategori Kendaraan</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $unit->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Pricing & Status Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-4 border-t border-slate-100">
                <div>
                    <label for="price_per_hour" class="block text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.unit_price_per_hour') }} (Rp) *</label>
                    <input type="number" 
                           id="price_per_hour" 
                           name="price_per_hour" 
                           required 
                           min="0"
                           value="{{ old('price_per_hour', $unit->price_per_hour) }}"
                           placeholder="35000"
                           class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                </div>

                <div>
                    <label for="price_per_day" class="block text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.unit_price_per_day') }} (Rp) *</label>
                    <input type="number" 
                           id="price_per_day" 
                           name="price_per_day" 
                           required 
                           min="0"
                           value="{{ old('price_per_day', $unit->price_per_day) }}"
                           placeholder="350000"
                           class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                </div>

                <div>
                    <label for="late_fee_per_hour" class="block text-sm font-semibold text-slate-700">Denda per Jam (Rp)</label>
                    <input type="number" 
                           id="late_fee_per_hour" 
                           name="late_fee_per_hour" 
                           min="0"
                           value="{{ old('late_fee_per_hour', $unit->late_fee_per_hour) }}"
                           placeholder="Default: {{ config('rental-hub.late_fee_per_hour', 50000) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                    <p class="text-xs text-slate-400 mt-1">Kosongkan jika mengikuti default sistem.</p>
                </div>
            </div>

            <!-- Status & Photo Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-slate-100">
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.common_status') }} *</label>
                    <select id="status" 
                            name="status" 
                            required 
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('status', $unit->status?->value ?? 'available') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="photo" class="block text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.unit_photo') }}</label>
                    <input type="file" 
                           id="photo" 
                           name="photo" 
                           accept="image/jpeg,image/png,image/webp"
                           class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-slate-400 mt-1">{{ __('rental-hub::rental.unit_photo_hint') }}</p>

                    @if ($isEdit && $unit->photo_path)
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ $unit->photo_url }}" alt="Preview" class="w-16 h-12 rounded-lg object-cover border border-slate-200">
                            <span class="text-xs text-slate-500">Foto aktif saat ini. Unggah file baru untuk mengganti.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Specifications (JSON Key-Value via Alpine.js) -->
            @php
                $initialSpecs = [];
                if (old('spec_keys')) {
                    $keys = old('spec_keys');
                    $values = old('spec_values', []);
                    foreach ($keys as $i => $k) {
                        $initialSpecs[] = ['key' => $k, 'val' => $values[$i] ?? ''];
                    }
                } elseif ($unit->specifications) {
                    foreach ($unit->specifications as $k => $v) {
                        $initialSpecs[] = ['key' => $k, 'val' => $v];
                    }
                }
                if (empty($initialSpecs)) {
                    $initialSpecs = [
                        ['key' => 'Transmisi', 'val' => 'Automatic'],
                        ['key' => 'Kapasitas', 'val' => '7 Kursi'],
                        ['key' => 'Bahan Bakar', 'val' => 'Bensin'],
                    ];
                }
            @endphp

            <div class="pt-4 border-t border-slate-100" 
                 x-data="{ 
                     specs: {{ json_encode($initialSpecs) }},
                     addSpec() { this.specs.push({ key: '', val: '' }); },
                     removeSpec(index) { this.specs.splice(index, 1); }
                 }">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">{{ __('rental-hub::rental.unit_specifications') }}</label>
                        <p class="text-xs text-slate-400">Atribut spesifikasi seperti transmisi, kapasitas, bahan bakar, dsb.</p>
                    </div>
                    <button type="button" 
                            @click="addSpec()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                        + Tambah Baris
                    </button>
                </div>

                <div class="space-y-2.5">
                    <template x-for="(spec, index) in specs" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   name="spec_keys[]" 
                                   x-model="spec.key"
                                   placeholder="Label (contoh: Transmisi)" 
                                   class="w-1/3 rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                            <input type="text" 
                                   name="spec_values[]" 
                                   x-model="spec.val"
                                   placeholder="Nilai (contoh: Automatic 6-Speed)" 
                                   class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                            <button type="button" 
                                    @click="removeSpec(index)" 
                                    class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors"
                                    title="Hapus baris">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('rental.units.index') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors min-h-[44px]">
                    {{ __('rental-hub::rental.common_cancel') }}
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm transition-all min-h-[44px]">
                    {{ __('rental-hub::rental.common_save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
