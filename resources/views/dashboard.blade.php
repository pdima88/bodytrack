@extends('layouts.app')

@section('title', __('app.dashboard.title') . ' — ' . config('app.name'))

@section('content')
<div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
    <h1 class="text-lg font-semibold text-slate-900 mb-2">{{ __('app.dashboard.empty_title') }}</h1>
    <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">{{ __('app.dashboard.empty_text') }}</p>
    <a href="#" class="inline-block rounded-lg bg-teal-600 px-5 py-2.5 text-white font-medium hover:bg-teal-700">
        {{ __('app.dashboard.add_measurement') }}
    </a>
</div>
@endsection
