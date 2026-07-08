@extends('layouts.guest')

@section('title', __('app.auth.forgot_title') . ' — ' . config('app.name'))

@section('content')
<h1 class="text-xl font-semibold text-slate-900 mb-2">{{ __('app.auth.forgot_title') }}</h1>
<p class="text-sm text-slate-500 mb-6">{{ __('app.auth.forgot_text') }}</p>

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium mb-1">{{ __('app.auth.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-white font-medium hover:bg-teal-700">
        {{ __('app.auth.forgot_button') }}
    </button>
</form>

<p class="mt-6 text-sm text-slate-500 text-center">
    <a href="{{ route('login') }}" class="text-teal-700 hover:underline">{{ __('app.auth.back_to_login') }}</a>
</p>
@endsection
