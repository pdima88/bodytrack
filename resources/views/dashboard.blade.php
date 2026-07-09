@extends('layouts.app')

@section('title', __('app.dashboard.title') . ' — ' . config('app.name'))

@section('content')
@if ($latest === null)
    <div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
        <h1 class="text-lg font-semibold text-slate-900 mb-2">{{ __('app.dashboard.empty_title') }}</h1>
        <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">{{ __('app.dashboard.empty_text') }}</p>
        <a href="{{ route('measurements.create') }}" class="inline-block rounded-lg bg-teal-600 px-5 py-2.5 text-white font-medium hover:bg-teal-700">
            {{ __('app.dashboard.add_measurement') }}
        </a>
    </div>
@else
    @php
        $badge = fn (string $status) => match ($status) {
            'normal' => 'bg-teal-50 text-teal-700',
            'low', 'high' => 'bg-amber-50 text-amber-700',
            'very_high' => 'bg-red-50 text-red-700',
            default => 'text-slate-400',
        };
        $fmt = fn ($v, $dec = 1) => number_format($v, $dec, ',', ' ');
        $infoButton = fn (string $key) => '<button type="button" onclick="document.getElementById(\'info-' . $key . '\').showModal()"'
            . ' class="absolute top-3 right-3 w-5 h-5 rounded-full border border-slate-200 text-[11px] leading-none text-slate-400 hover:text-teal-700 hover:border-teal-300 flex items-center justify-center"'
            . ' aria-label="' . e(__('app.metric_info.about')) . '">?</button>';
    @endphp

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ __('app.dashboard.title') }}</h1>
            <p class="text-sm text-slate-500">{{ __('app.dashboard.last_measured') }}: {{ $latest->measured_at->format('d.m.Y H:i') }}</p>
        </div>
        <a href="{{ route('measurements.create') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm text-white font-medium hover:bg-teal-700">
            {{ __('app.dashboard.add_measurement') }}
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="relative bg-white rounded-xl border border-slate-200 p-4">
            {!! $infoButton('weight_kg') !!}
            <p class="text-xs text-slate-500 flex items-center gap-1.5 pr-6"><x-metric-icon name="weight_kg" class="text-teal-600"/>{{ __('app.measurements.weight_short') }}</p>
            <p class="text-2xl font-semibold text-slate-900 mt-0.5">{{ $fmt($latest->weight_kg) }} <span class="text-sm font-normal text-slate-400">{{ __('app.units.kg') }}</span></p>
            @if (isset($weekDeltas['weight_kg']))
                <p class="text-xs mt-1 {{ $weekDeltas['weight_kg'] <= 0 ? 'text-teal-600' : 'text-amber-600' }}">
                    {{ $weekDeltas['weight_kg'] > 0 ? '+' : '' }}{{ $fmt($weekDeltas['weight_kg']) }} {{ __('app.dashboard.per_week') }}
                </p>
            @elseif ($profile->target_weight_kg)
                <p class="text-xs mt-1 text-slate-400">{{ __('app.dashboard.target') }}: {{ $fmt($profile->target_weight_kg) }}</p>
            @endif
        </div>

        @foreach (['fat_percent', 'water_percent', 'muscle_percent', 'bone_percent', 'visceral_fat', 'bmi'] as $metric)
            @if (isset($metrics[$metric]))
                @php $data = $metrics[$metric]; @endphp
                <div class="relative bg-white rounded-xl border border-slate-200 p-4">
                    {!! $infoButton($metric) !!}
                    <p class="text-xs text-slate-500 flex items-center gap-1.5 pr-6"><x-metric-icon :name="$metric" class="text-teal-600"/>{{ __('app.metrics.' . $metric) }}</p>
                    <p class="text-2xl font-semibold text-slate-900 mt-0.5">
                        {{ $fmt($data['value'], $metric === 'visceral_fat' ? 0 : 1) }}
                        @if (str_ends_with($metric, '_percent'))
                            <span class="text-sm font-normal text-slate-400">%</span>
                        @endif
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $badge($data['status']) }}">
                            @if ($metric === 'bmi')
                                {{ __('app.bmi.' . app(\App\Services\BodyNorms::class)->bmiCategory($data['value'])) }}
                            @else
                                {{ __('app.status.' . $data['status']) }}
                            @endif
                        </span>
                        @if ($data['range'][1] !== null)
                            <span class="text-xs text-slate-400">{{ $fmt($data['range'][0]) }}–{{ $fmt($data['range'][1]) }}</span>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach

        @if (isset($metrics['bmr_kcal']))
            <div class="relative bg-white rounded-xl border border-slate-200 p-4">
                {!! $infoButton('bmr_kcal') !!}
                <p class="text-xs text-slate-500 flex items-center gap-1.5 pr-6"><x-metric-icon name="bmr_kcal" class="text-teal-600"/>{{ __('app.metrics.bmr_kcal') }}</p>
                <p class="text-2xl font-semibold text-slate-900 mt-0.5">{{ $metrics['bmr_kcal']['value'] }} <span class="text-sm font-normal text-slate-400">{{ __('app.units.kcal') }}</span></p>
                <p class="text-xs mt-1 text-slate-400">{{ __('app.dashboard.bmr_estimate') }}: ~{{ $metrics['bmr_kcal']['range'][0] }}</p>
            </div>
        @endif
    </div>

    <div class="grid lg:grid-cols-5 gap-4">
        <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">{{ __('app.dashboard.weight_chart') }}</h2>
                <a href="{{ route('charts') }}" class="text-sm text-teal-700 hover:underline">{{ __('app.dashboard.all_charts') }}</a>
            </div>
            <div class="relative h-64">
                <canvas id="weight-chart"></canvas>
            </div>
        </div>

        @foreach (array_unique(array_merge(['weight_kg'], array_keys($metrics))) as $infoKey)
            <dialog id="info-{{ $infoKey }}" class="m-auto w-[calc(100%-2rem)] max-w-md rounded-xl p-0 shadow-xl backdrop:bg-slate-900/40">
                <div class="p-6">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h3 class="flex items-center gap-2 font-semibold text-slate-900">
                            <x-metric-icon :name="$infoKey" class="text-teal-600"/>{{ __('app.metrics.' . $infoKey) }}
                        </h3>
                        <button type="button" onclick="this.closest('dialog').close()"
                                class="text-2xl leading-none text-slate-400 hover:text-slate-700"
                                aria-label="{{ __('app.metric_info.close') }}">&times;</button>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ __('app.metric_info.' . $infoKey) }}</p>
                </div>
            </dialog>
        @endforeach

        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">{{ __('app.dashboard.recommendations') }}</h2>
            <ul class="space-y-3">
                @foreach ($recommendations as $rec)
                    @php
                        $dot = match ($rec['severity']) {
                            'good' => 'bg-teal-500',
                            'warning' => 'bg-amber-500',
                            default => 'bg-slate-300',
                        };
                    @endphp
                    <li class="flex gap-2.5 text-sm text-slate-600 leading-relaxed">
                        <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $dot }}"></span>
                        <span>{{ __('app.recommendations.' . $rec['key'], $rec['params']) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
@endsection

@push('scripts')
@if ($latest !== null)
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
    const chartData = @json($chart);
    const datasets = [
        {
            label: @json(__('app.metrics.weight_kg')),
            data: chartData.points,
            borderColor: '#0d9488',
            backgroundColor: '#0d9488',
            pointRadius: 3,
            tension: 0.3,
        },
        {
            label: @json(__('app.dashboard.moving_average')),
            data: chartData.movingAverage,
            borderColor: '#94a3b8',
            borderDash: [6, 4],
            pointRadius: 0,
            tension: 0.3,
        },
    ];

    if (chartData.target) {
        datasets.push({
            label: @json(__('app.dashboard.target')),
            data: chartData.points.length ? [
                {x: chartData.points[0].x, y: chartData.target},
                {x: chartData.points[chartData.points.length - 1].x, y: chartData.target},
            ] : [],
            borderColor: '#f59e0b',
            borderDash: [2, 3],
            pointRadius: 0,
        });
    }

    document.querySelectorAll('dialog').forEach(d => d.addEventListener('click', e => {
        if (e.target === d) d.close();
    }));

    new Chart(document.getElementById('weight-chart'), {
        type: 'line',
        data: {datasets},
        options: {
            maintainAspectRatio: false,
            interaction: {mode: 'nearest', intersect: false},
            scales: {
                x: {type: 'category', ticks: {maxTicksLimit: 8}, grid: {display: false}},
                y: {grace: '10%'},
            },
            plugins: {legend: {labels: {boxWidth: 12, boxHeight: 2}}},
        },
    });
</script>
@endif
@endpush
