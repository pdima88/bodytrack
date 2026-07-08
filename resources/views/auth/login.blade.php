@extends('layouts.guest')

@section('title', __('app.auth.login_title') . ' — ' . config('app.name'))

@section('content')
<h1 class="text-xl font-semibold text-slate-900 mb-6">{{ __('app.auth.login_title') }}</h1>

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium mb-1">{{ __('app.auth.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium mb-1">{{ __('app.auth.password') }}</label>
        <input id="password" type="password" name="password" required autocomplete="current-password"
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
            {{ __('app.auth.remember') }}
        </label>
        <a href="{{ route('password.request') }}" class="text-sm text-teal-700 hover:underline">{{ __('app.auth.forgot') }}</a>
    </div>

    <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-white font-medium hover:bg-teal-700">
        {{ __('app.auth.login_button') }}
    </button>
</form>

<p class="mt-6 text-sm text-slate-500 text-center">
    {{ __('app.auth.no_account') }}
    <a href="{{ route('register') }}" class="text-teal-700 hover:underline">{{ __('app.auth.register_link') }}</a>
</p>
@endsection
