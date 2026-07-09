@extends('layouts.app')

@section('title', ($measurement ? __('app.measurements.edit_title') : __('app.measurements.add_title')) . ' — ' . config('app.name'))

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-slate-900 mb-1">
            {{ $measurement ? __('app.measurements.edit_title') : __('app.measurements.add_title') }}
        </h1>
        <p class="text-sm text-slate-500 mb-6">{{ __('app.measurements.intro') }}</p>

        @if (session('anomaly'))
            <div class="mb-4 rounded-lg bg-amber-50 border border-amber-300 px-4 py-3 text-sm text-amber-800">
                {{ session('anomaly') }}
            </div>
        @endif

        <form method="POST"
              action="{{ $measurement ? route('measurements.update', $measurement) : route('measurements.store') }}"
              class="space-y-4">
            @csrf
            @if ($measurement) @method('PUT') @endif

            <div>
                <label for="measured_at" class="block text-sm font-medium mb-1">{{ __('app.measurements.measured_at') }}</label>
                <input id="measured_at" type="datetime-local" name="measured_at" required
                       value="{{ old('measured_at', ($measurement?->measured_at ?? now())->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                @error('measured_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="weight_kg" class="flex items-center gap-1.5 text-sm font-medium mb-1"><x-metric-icon name="weight_kg" class="w-4 h-4 text-teal-600"/>{{ __('app.measurements.weight') }}</label>
                <input id="weight_kg" type="number" name="weight_kg" step="0.1" min="10" max="180" required autofocus
                       value="{{ old('weight_kg', $measurement?->weight_kg) }}"
                       @if($last) placeholder="{{ __('app.measurements.last_value') }}: {{ number_format($last->weight_kg, 1, ',', ' ') }}" @endif
                       class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500 text-lg font-medium">
                @error('weight_kg') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <p class="text-xs text-slate-400 pt-2 border-t border-slate-100">{{ __('app.measurements.optional_hint') }}</p>

            <div class="grid grid-cols-2 gap-3">
                @foreach ([
                    'fat_percent' => ['step' => '0.1', 'min' => 3, 'max' => 75],
                    'water_percent' => ['step' => '0.1', 'min' => 20, 'max' => 85],
                    'muscle_percent' => ['step' => '0.1', 'min' => 10, 'max' => 75],
                    'bone_percent' => ['step' => '0.1', 'min' => 1, 'max' => 15],
                    'visceral_fat' => ['step' => '1', 'min' => 1, 'max' => 59],
                    'bmi' => ['step' => '0.1', 'min' => 8, 'max' => 80],
                    'bmr_kcal' => ['step' => '1', 'min' => 500, 'max' => 5000],
                ] as $field => $attrs)
                    <div>
                        <label for="{{ $field }}" class="flex items-center gap-1.5 text-sm font-medium mb-1"><x-metric-icon :name="$field" class="w-4 h-4 text-teal-600"/>{{ __('app.measurements.' . $field) }}</label>
                        <input id="{{ $field }}" type="number" name="{{ $field }}"
                               step="{{ $attrs['step'] }}" min="{{ $attrs['min'] }}" max="{{ $attrs['max'] }}"
                               value="{{ old($field, $measurement?->{$field}) }}"
                               class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                        @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>

            @if (session('anomaly'))
                <label class="flex items-start gap-2 rounded-lg border border-amber-300 bg-amber-50 px-3 py-2.5 text-sm text-amber-800 cursor-pointer">
                    <input type="checkbox" name="confirm_anomaly" value="1" class="mt-0.5 rounded border-amber-400 text-amber-600 focus:ring-amber-500">
                    {{ __('app.measurements.anomaly_confirm') }}
                </label>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="flex-1 rounded-lg bg-teal-600 px-4 py-2.5 text-white font-medium hover:bg-teal-700">
                    {{ __('app.measurements.save') }}
                </button>
                @if ($measurement)
                    <a href="{{ route('measurements.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-slate-600 font-medium hover:bg-slate-50">
                        {{ __('app.measurements.cancel') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
