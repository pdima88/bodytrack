@extends('layouts.guest')

@section('title', __('app.auth.register_title') . ' — ' . config('app.name'))

@section('content')
<h1 class="text-xl font-semibold text-slate-900 mb-6">{{ __('app.auth.register_title') }}</h1>

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium mb-1">{{ __('app.auth.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium mb-1">{{ __('app.auth.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium mb-1">{{ __('app.auth.password') }}</label>
        <input id="password" type="password" name="password" required autocomplete="new-password"
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium mb-1">{{ __('app.auth.password_confirm') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
    </div>

    <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-white font-medium hover:bg-teal-700">
        {{ __('app.auth.register_button') }}
    </button>
</form>

<p class="mt-6 text-sm text-slate-500 text-center">
    {{ __('app.auth.have_account') }}
    <a href="{{ route('login') }}" class="text-teal-700 hover:underline">{{ __('app.auth.login_link') }}</a>
</p>
@endsection
