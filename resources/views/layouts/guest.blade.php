<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8">
        <a href="{{ url('/') }}" class="flex items-center gap-2 mb-6 text-slate-900">
            <svg class="w-8 h-8 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18z"/>
                <path d="M12 7a5 5 0 0 1 4.9 4H7.1A5 5 0 0 1 12 7z"/>
                <circle cx="12" cy="11" r="1"/>
            </svg>
            <span class="text-xl font-semibold">{{ config('app.name') }}</span>
        </a>

        <div class="w-full max-w-md bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-teal-50 border border-teal-200 px-4 py-3 text-sm text-teal-800">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>

        <div class="mt-4 flex text-xs border border-slate-200 rounded-lg overflow-hidden">
            @foreach (\App\Http\Middleware\SetLocale::SUPPORTED as $loc)
                <a href="{{ route('locale.switch', $loc) }}"
                   class="px-2.5 py-1 {{ app()->getLocale() === $loc ? 'bg-white font-medium text-slate-900' : 'text-slate-400 hover:text-slate-700' }}">
                    {{ strtoupper($loc) }}
                </a>
            @endforeach
        </div>

        <p class="mt-4 text-xs text-slate-400 max-w-md text-center">{{ __('app.disclaimer') }}</p>
    </div>
</body>
</html>
