@extends('layouts.guest')

@section('title', __('app.auth.confirm_title') . ' — ' . config('app.name'))

@section('content')
<h1 class="text-xl font-semibold text-slate-900 mb-2">{{ __('app.auth.confirm_title') }}</h1>
<p class="text-sm text-slate-500 mb-6">{{ __('app.auth.confirm_text') }}</p>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
    @csrf

    <div>
        <label for="password" class="block text-sm font-medium mb-1">{{ __('app.auth.password') }}</label>
        <input id="password" type="password" name="password" required autofocus autocomplete="current-password"
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-white font-medium hover:bg-teal-700">
        {{ __('app.auth.confirm_button') }}
    </button>
</form>
@endsection
