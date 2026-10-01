@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.categories_title'))
@section('page-title', __('rental-hub::rental.categories_title'))

@section('content')
<div class="space-y-6" x-data="{ 
    modalOpen: false, 
    isEdit: false, 
    actionUrl: '{{ route('rental.categories.store') }}',
    categoryName: '', 
    categorySlug: '', 
    categoryDesc: '',
    openCreate() {
        this.isEdit = false;
        this.actionUrl = '{{ route('rental.categories.store') }}';
        this.categoryName = '';
        this.categorySlug = '';
        this.categoryDesc = '';
        this.modalOpen = true;
    },
    openEdit(cat) {
        this.isEdit = true;
        this.actionUrl = '{{ route('rental.categories.index') }}/' + cat.id;
        this.categoryName = cat.name;
        this.categorySlug = cat.slug;
        this.categoryDesc = cat.description || '';
        this.modalOpen = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ __('rental-hub::rental.categories_title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('rental-hub::rental.categories_subtitle') }}</p>
        </div>
        <div>
            <button type="button" 
                    @click="openCreate()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm transition-colors min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ __('rental-hub::rental.category_add') }}</span>
            </button>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">{{ __('rental-hub::rental.category_name') }}</th>
                        <th class="px-6 py-3.5 text-left">{{ __('rental-hub::rental.category_slug') }}</th>
                        <th class="px-6 py-3.5 text-left">{{ __('rental-hub::rental.category_description') }}</th>
                        <th class="px-6 py-3.5 text-center">{{ __('rental-hub::rental.category_units_count') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('rental-hub::rental.common_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $cat)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                                {{ $cat->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs whitespace-nowrap">
                                {{ $cat->slug }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs max-w-xs truncate">
                                {{ $cat->description ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $cat->units_count }} Unit
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                <button type="button" 
                                        @click="openEdit({{ json_encode($cat) }})"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 p-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                                    {{ __('rental-hub::rental.common_edit') }}
                                </button>

                                <form method="POST" action="{{ route('rental.categories.destroy', $cat) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 p-1.5 rounded-lg hover:bg-rose-50 transition-colors">
                                        {{ __('rental-hub::rental.common_delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                {{ __('rental-hub::rental.common_no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Category Modal (Create / Edit) -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                 @click="modalOpen = false"></div>

            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 sm:p-8">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <h3 class="text-base font-bold text-slate-900" x-text="isEdit ? 'Ubah Kategori' : 'Tambah Kategori Baru'"></h3>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label for="modal_cat_name" class="block text-sm font-semibold text-slate-700">Nama Kategori *</label>
                        <input type="text" 
                               id="modal_cat_name" 
                               name="name" 
                               x-model="categoryName"
                               required 
                               placeholder="Contoh: MPV & Mobil Keluarga"
                               class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                    </div>

                    <div>
                        <label for="modal_cat_slug" class="block text-sm font-semibold text-slate-700">Slug URL (Opsional)</label>
                        <input type="text" 
                               id="modal_cat_slug" 
                               name="slug" 
                               x-model="categorySlug"
                               placeholder="otomatis dari nama kategori"
                               class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-mono text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                    </div>

                    <div>
                        <label for="modal_cat_desc" class="block text-sm font-semibold text-slate-700">Deskripsi Singkat</label>
                        <textarea id="modal_cat_desc" 
                                  name="description" 
                                  x-model="categoryDesc"
                                  rows="3" 
                                  placeholder="Keterangan peruntukan armada pada kategori ini..."
                                  class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" 
                                @click="modalOpen = false" 
                                class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors min-h-[44px]">
                            {{ __('rental-hub::rental.common_cancel') }}
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-5 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors min-h-[44px]">
                            {{ __('rental-hub::rental.common_save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
