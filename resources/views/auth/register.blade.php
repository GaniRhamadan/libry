@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.register_title'))

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-900">{{ __('rental-hub::rental.register_title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('rental-hub::rental.register_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('rental.register.submit') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
            <div class="mt-1.5">
                <input id="name" 
                       name="name" 
                       type="text" 
                       required 
                       value="{{ old('name') }}"
                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm shadow-sm transition-colors"
                       placeholder="Nama lengkap Anda">
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email</label>
            <div class="mt-1.5">
                <input id="email" 
                       name="email" 
                       type="email" 
                       required 
                       value="{{ old('email') }}"
                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm shadow-sm transition-colors"
                       placeholder="nama@email.com">
            </div>
        </div>

        <div>
            <label for="phone" class="block text-sm font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
            <div class="mt-1.5">
                <input id="phone" 
                       name="phone" 
                       type="text" 
                       value="{{ old('phone') }}"
                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm shadow-sm transition-colors"
                       placeholder="081234567890">
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700">Kata Sandi</label>
            <div class="mt-1.5">
                <input id="password" 
                       name="password" 
                       type="password" 
                       required 
                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm shadow-sm transition-colors"
                       placeholder="Minimal 8 karakter">
            </div>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Ulangi Kata Sandi</label>
            <div class="mt-1.5">
                <input id="password_confirmation" 
                       name="password_confirmation" 
                       type="password" 
                       required 
                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm shadow-sm transition-colors"
                       placeholder="Konfirmasi kata sandi Anda">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full flex justify-center items-center py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm transition-all min-h-[44px]">
                {{ __('rental-hub::rental.nav_register') }}
            </button>
        </div>
    </form>

    <div class="text-center text-sm text-slate-500 pt-2 border-t border-slate-100">
        Sudah memiliki akun sewa?
        <a href="{{ route('rental.login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors ml-1">
            {{ __('rental-hub::rental.nav_login') }}
        </a>
    </div>
</div>
@endsection
