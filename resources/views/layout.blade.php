<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('rental-hub::rental.app_name')) - {{ __('rental-hub::rental.app_name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @if (config('rental-hub.use_cdn', true))
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#EEF2FF',
                                100: '#E0E7FF',
                                500: '#6366F1',
                                600: '#4F46E5',
                                700: '#4338CA',
                            }
                        }
                    }
                }
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @else
        <link rel="stylesheet" href="{{ asset('vendor/rental-hub/app.css') }}">
        <script defer src="{{ asset('vendor/rental-hub/app.js') }}"></script>
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased" x-data="{ mobileMenuOpen: false }">
    @auth
        <!-- Mobile Sidebar Drawer Backdrop -->
        <div x-show="mobileMenuOpen" 
             x-cloak 
             class="relative z-50 md:hidden" 
             role="dialog" 
             aria-modal="true">
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                 @click="mobileMenuOpen = false"></div>

            <div class="fixed inset-0 flex">
                <div x-show="mobileMenuOpen"
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4 shadow-xl">
                    <div class="absolute top-0 right-0 -mr-12 pt-4">
                        <button type="button" 
                                @click="mobileMenuOpen = false"
                                class="ml-1 flex h-10 w-10 items-center justify-center rounded-full text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Tutup navigasi</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-shrink-0 items-center px-5 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 text-white font-bold text-lg shadow-sm">
                                RH
                            </div>
                            <div>
                                <h1 class="text-base font-bold text-slate-900 leading-tight">{{ __('rental-hub::rental.app_name') }}</h1>
                                <span class="text-xs text-slate-500">{{ auth()->user()->rental_role === 'admin' ? __('rental-hub::rental.role_admin') : __('rental-hub::rental.role_customer') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex-1 h-0 overflow-y-auto px-3">
                        @include('rental-hub::partials.nav-links')
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Static Sidebar -->
        <aside class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col md:border-r md:border-slate-200 md:bg-white">
            <div class="flex flex-grow flex-col overflow-y-auto">
                <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white font-bold text-base shadow-sm">
                        RH
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-900 leading-tight">{{ __('rental-hub::rental.app_name') }}</div>
                        <div class="text-xs text-slate-500 font-medium">
                            {{ auth()->user()->rental_role === 'admin' ? __('rental-hub::rental.role_admin') : __('rental-hub::rental.role_customer') }}
                        </div>
                    </div>
                </div>
                <div class="flex-1 px-4 py-6 space-y-1">
                    @include('rental-hub::partials.nav-links')
                </div>
                <div class="border-t border-slate-100 p-4">
                    <div class="flex items-center justify-between">
                        <div class="truncate mr-2">
                            <p class="text-xs font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('rental.logout') }}">
                            @csrf
                            <button type="submit" 
                                    title="{{ __('rental-hub::rental.nav_logout') }}"
                                    class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors focus:ring-2 focus:ring-rose-500 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <div class="flex flex-1 flex-col md:pl-64 min-h-screen">
            <!-- Top Navbar -->
            <header class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white/95 backdrop-blur border-b border-slate-200">
                <button type="button" 
                        @click="mobileMenuOpen = true"
                        class="px-4 text-slate-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden"
                        aria-label="Buka menu navigasi">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex flex-1 justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <h2 class="text-lg font-semibold text-slate-900 truncate">
                            @yield('page-title', __('rental-hub::rental.nav_dashboard'))
                        </h2>
                    </div>

                    <div class="ml-4 flex items-center md:ml-6 gap-3">
                        <a href="{{ route('rental.profile.edit') }}" 
                           class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-slate-700 hover:text-indigo-600 px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                            <span class="inline-block h-6 w-6 rounded-full bg-slate-200 text-center font-bold text-slate-600 leading-6 text-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden sm:inline-block max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 pb-12">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
                    <!-- Flash Feedback Messages -->
                    @if (session('success'))
                        <div class="mb-6 flex items-start gap-3 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800" role="alert">
                            <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 font-medium">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 flex items-start gap-3 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800" role="alert">
                            <svg class="w-5 h-5 flex-shrink-0 text-rose-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800" role="alert">
                            <div class="font-semibold mb-1">Periksa kembali data yang dimasukkan:</div>
                            <ul class="list-disc pl-5 space-y-1 text-xs sm:text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    @else
        <!-- Guest Content (Login, Register) -->
        <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold text-xl shadow-md">
                    RH
                </div>
                <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">{{ __('rental-hub::rental.app_name') }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ __('rental-hub::rental.app_tagline') }}</p>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                @if (session('success'))
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800" role="alert">
                        <ul class="list-disc pl-5 space-y-1 text-xs sm:text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white py-8 px-6 shadow-sm border border-slate-200 rounded-2xl sm:px-10">
                    @yield('content')
                </div>
            </div>
        </div>
    @endauth

    @stack('scripts')
</body>
</html>
