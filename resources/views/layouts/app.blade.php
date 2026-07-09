<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/ui.js') }}" defer></script>
    @stack('head')
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between gap-4">
            <a href="{{ route('dashboard') }}" data-loader class="flex items-center gap-2 shrink-0 text-slate-900">
                <svg class="w-7 h-7 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18z"/>
                    <path d="M12 7a5 5 0 0 1 4.9 4H7.1A5 5 0 0 1 12 7z"/>
                    <circle cx="12" cy="11" r="1"/>
                </svg>
                <span class="font-semibold hidden sm:inline">{{ config('app.name') }}</span>
            </a>

            <nav class="flex items-center gap-1 text-sm overflow-x-auto">
                <a href="{{ route('dashboard') }}" data-loader class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-slate-100 font-medium text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">{{ __('app.nav.dashboard') }}</a>
                <a href="{{ route('measurements.index') }}" data-loader class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('measurements.*') ? 'bg-slate-100 font-medium text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">{{ __('app.nav.history') }}</a>
                <a href="{{ route('charts') }}" data-loader class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('charts') ? 'bg-slate-100 font-medium text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">{{ __('app.nav.charts') }}</a>
                <a href="{{ route('profile.edit') }}" data-loader class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('profile.*') ? 'bg-slate-100 font-medium text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">{{ __('app.nav.profile') }}</a>
            </nav>

            <div class="flex items-center gap-3 shrink-0">
                <div class="flex text-xs border border-slate-200 rounded-lg overflow-hidden">
                    @foreach (\App\Http\Middleware\SetLocale::SUPPORTED as $loc)
                        <a href="{{ route('locale.switch', $loc) }}" data-loader
                           class="px-2 py-1 {{ app()->getLocale() === $loc ? 'bg-slate-100 font-medium text-slate-900' : 'text-slate-400 hover:text-slate-700' }}">
                            {{ strtoupper($loc) }}
                        </a>
                    @endforeach
                </div>
                <span class="text-sm text-slate-500 hidden md:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-slate-500 hover:text-slate-900" title="{{ __('app.nav.logout') }}">{{ __('app.nav.logout') }}</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-teal-50 border border-teal-200 px-4 py-3 text-sm text-teal-800">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="max-w-5xl mx-auto px-4 py-6">
        <p class="text-xs text-slate-400">{{ __('app.disclaimer') }}</p>
    </footer>
    @stack('scripts')
</body>
</html>
