@extends('layouts.app')

@section('title', __('app.profile.title') . ' — ' . config('app.name'))

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8">
        <h1 class="text-xl font-semibold text-slate-900 mb-1">{{ __('app.profile.title') }}</h1>
        <p class="text-sm text-slate-500 mb-6">{{ __('app.profile.intro') }}</p>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <span class="block text-sm font-medium mb-1">{{ __('app.profile.sex') }}</span>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (\App\Models\Profile::SEXES as $sex)
                        <label class="flex items-center justify-center gap-2 rounded-lg border px-3 py-2.5 text-sm cursor-pointer has-checked:border-teal-600 has-checked:bg-teal-50 has-checked:text-teal-800 border-slate-300">
                            <input type="radio" name="sex" value="{{ $sex }}" required
                                   @checked(old('sex', $profile?->sex) === $sex)
                                   class="text-teal-600 focus:ring-teal-500">
                            {{ __('app.profile.sex_' . $sex) }}
                        </label>
                    @endforeach
                </div>
                @error('sex') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="birth_date" class="block text-sm font-medium mb-1">{{ __('app.profile.birth_date') }}</label>
                    <input id="birth_date" type="date" name="birth_date" required
                           value="{{ old('birth_date', $profile?->birth_date?->format('Y-m-d')) }}"
                           class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                    @error('birth_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="height_cm" class="block text-sm font-medium mb-1">{{ __('app.profile.height') }}</label>
                    <input id="height_cm" type="number" name="height_cm" min="100" max="250" required
                           value="{{ old('height_cm', $profile?->height_cm) }}"
                           class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                    @error('height_cm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="activity_level" class="block text-sm font-medium mb-1">{{ __('app.profile.activity') }}</label>
                <select id="activity_level" name="activity_level" required
                        class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                    @foreach (\App\Models\Profile::ACTIVITY_LEVELS as $level)
                        <option value="{{ $level }}" @selected(old('activity_level', $profile?->activity_level ?? 'moderate') === $level)>
                            {{ __('app.profile.activity_' . $level) }}
                        </option>
                    @endforeach
                </select>
                @error('activity_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="target_weight_kg" class="block text-sm font-medium mb-1">
                    {{ __('app.profile.target_weight') }}
                    <span class="text-slate-400 font-normal">({{ __('app.profile.optional') }})</span>
                </label>
                <input id="target_weight_kg" type="number" name="target_weight_kg" step="0.1" min="20" max="300"
                       value="{{ old('target_weight_kg', $profile?->target_weight_kg) }}"
                       class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                @error('target_weight_kg') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-white font-medium hover:bg-teal-700">
                {{ __('app.profile.save') }}
            </button>
        </form>
    </div>
</div>
@endsection
