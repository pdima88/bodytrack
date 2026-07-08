@extends('layouts.guest')

@section('title', __('app.auth.verify_title') . ' — ' . config('app.name'))

@section('content')
<h1 class="text-xl font-semibold text-slate-900 mb-2">{{ __('app.auth.verify_title') }}</h1>
<p class="text-sm text-slate-500 mb-6">{{ __('app.auth.verify_text') }}</p>

@if (session('status') === 'verification-link-sent')
    <div class="mb-4 rounded-lg bg-teal-50 border border-teal-200 px-4 py-3 text-sm text-teal-800">
        {{ __('app.auth.verify_sent') }}
    </div>
@endif

<div class="space-y-3">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-white font-medium hover:bg-teal-700">
            {{ __('app.auth.verify_resend') }}
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-600 font-medium hover:bg-slate-50">
            {{ __('app.nav.logout') }}
        </button>
    </form>
</div>
@endsection
