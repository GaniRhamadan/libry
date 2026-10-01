@extends('rental-hub::layout')

@section('title', __('rental-hub::rental.login_title'))

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-900">{{ __('rental-hub::rental.login_title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('rental-hub::rental.login_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('rental.login.submit') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email</label>
            <div class="mt-1.5">
                <input id="email" 
                       name="email" 
                       type="email" 
                       autocomplete="email" 
                       required 
                       value="{{ old('email') }}"
                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm shadow-sm transition-colors"
                       placeholder="nama@email.com">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-semibold text-slate-700">Kata Sandi</label>
            </div>
            <div class="mt-1.5">
                <input id="password" 
                       name="password" 
                       type="password" 
                       autocomplete="current-password" 
                       required 
                       class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm shadow-sm transition-colors"
                       placeholder="Masukkan kata sandi Anda">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" 
                       name="remember" 
                       type="checkbox" 
                       class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="remember" class="ml-2 block text-sm text-slate-600">Ingat saya di perangkat ini</label>
            </div>
        </div>

        <div>
            <button type="submit" 
                    class="w-full flex justify-center items-center py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm transition-all min-h-[44px]">
                {{ __('rental-hub::rental.nav_login') }}
            </button>
        </div>
    </form>

    <div class="text-center text-sm text-slate-500 pt-2 border-t border-slate-100">
        Belum memiliki akun sewa?
        <a href="{{ route('rental.register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors ml-1">
            {{ __('rental-hub::rental.register_title') }}
        </a>
    </div>
</div>
@endsection
