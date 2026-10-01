@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.profile_title'))
@section('page-title', __('rental-hub::rental.profile_title'))

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-5 mb-6">
            <h3 class="text-base font-bold text-slate-900">{{ __('rental-hub::rental.profile_title') }}</h3>
            <p class="text-sm text-slate-500 mt-1">{{ __('rental-hub::rental.profile_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('rental.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           value="{{ old('name', $user->name) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           value="{{ old('email', $user->email) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $user->phone) }}"
                           class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm"
                           placeholder="081234567890">
                </div>
            </div>

            <!-- Password Change Section -->
            <div class="pt-6 border-t border-slate-100">
                <h4 class="text-sm font-bold text-slate-900 mb-1">Ganti Kata Sandi (Opsional)</h4>
                <p class="text-xs text-slate-500 mb-4">Kosongkan kolom sandi jika Anda tidak bermaksud mengubah kata sandi akun.</p>

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-slate-700">Kata Sandi Saat Ini</label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm"
                               placeholder="Verifikasi kata sandi lama">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700">Kata Sandi Baru</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm"
                                   placeholder="Minimal 8 karakter">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Ulangi Kata Sandi Baru</label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm"
                                   placeholder="Konfirmasi sandi baru">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm transition-all min-h-[44px]">
                    {{ __('rental-hub::rental.common_save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
