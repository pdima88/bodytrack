@extends('layouts.app')

@section('title', __('app.measurements.history_title') . ' — ' . config('app.name'))

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold text-slate-900">{{ __('app.measurements.history_title') }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('measurements.export') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 font-medium hover:bg-slate-50">
            {{ __('app.measurements.export') }}
        </a>
        <a href="{{ route('measurements.create') }}" data-loader class="rounded-lg bg-teal-600 px-4 py-2 text-sm text-white font-medium hover:bg-teal-700">
            {{ __('app.dashboard.add_measurement') }}
        </a>
    </div>
</div>

@if ($measurements->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
        <p class="text-sm text-slate-500">{{ __('app.measurements.empty') }}</p>
    </div>
@else
    <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm whitespace-nowrap">
            <thead>
                <tr class="border-b border-slate-200 text-left text-xs text-slate-500">
                    <th class="px-4 py-3 font-medium">{{ __('app.measurements.measured_at') }}</th>
                    <th class="px-3 py-3 font-medium text-right">{{ __('app.measurements.weight_short') }}</th>
                    <th class="px-3 py-3 font-medium text-right">{{ __('app.measurements.fat_short') }}</th>
                    <th class="px-3 py-3 font-medium text-right">{{ __('app.measurements.water_short') }}</th>
                    <th class="px-3 py-3 font-medium text-right">{{ __('app.measurements.muscle_short') }}</th>
                    <th class="px-3 py-3 font-medium text-right">{{ __('app.measurements.bone_short') }}</th>
                    <th class="px-3 py-3 font-medium text-right">{{ __('app.measurements.visceral_short') }}</th>
                    <th class="px-3 py-3 font-medium text-right">{{ __('app.measurements.bmi') }}</th>
                    <th class="px-3 py-3 font-medium text-right">{{ __('app.measurements.bmr_short') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($measurements as $m)
                    <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                        <td class="px-4 py-2.5 text-slate-600">{{ $m->measured_at->format('d.m.Y H:i') }}</td>
                        <td class="px-3 py-2.5 text-right font-medium">{{ number_format($m->weight_kg, 1, ',', ' ') }}</td>
                        <td class="px-3 py-2.5 text-right">{{ $m->fat_percent !== null ? number_format($m->fat_percent, 1, ',', ' ') : '—' }}</td>
                        <td class="px-3 py-2.5 text-right">{{ $m->water_percent !== null ? number_format($m->water_percent, 1, ',', ' ') : '—' }}</td>
                        <td class="px-3 py-2.5 text-right">{{ $m->muscle_percent !== null ? number_format($m->muscle_percent, 1, ',', ' ') : '—' }}</td>
                        <td class="px-3 py-2.5 text-right">{{ $m->bone_percent !== null ? number_format($m->bone_percent, 1, ',', ' ') : '—' }}</td>
                        <td class="px-3 py-2.5 text-right">{{ $m->visceral_fat ?? '—' }}</td>
                        <td class="px-3 py-2.5 text-right">{{ $m->bmi !== null ? number_format($m->bmi, 1, ',', ' ') : '—' }}</td>
                        <td class="px-3 py-2.5 text-right">{{ $m->bmr_kcal ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('measurements.edit', $m) }}" class="text-teal-700 hover:underline">{{ __('app.measurements.edit') }}</a>
                                <form method="POST" action="{{ route('measurements.destroy', $m) }}"
                                      onsubmit="return confirm('{{ __('app.measurements.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">{{ __('app.measurements.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $measurements->links() }}
    </div>
@endif
@endsection
