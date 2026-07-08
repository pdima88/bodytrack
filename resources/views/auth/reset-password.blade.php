@extends('layouts.guest')

@section('title', __('app.auth.reset_title') . ' — ' . config('app.name'))

@section('content')
<h1 class="text-xl font-semibold text-slate-900 mb-6">{{ __('app.auth.reset_title') }}</h1>

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div>
        <label for="email" class="block text-sm font-medium mb-1">{{ __('app.auth.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium mb-1">{{ __('app.auth.new_password') }}</label>
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
        {{ __('app.auth.reset_button') }}
    </button>
</form>
@endsection
