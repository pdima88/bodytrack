@extends('layouts.app')

@section('title', __('app.charts.title') . ' — ' . config('app.name'))

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <h1 class="text-xl font-semibold text-slate-900">{{ __('app.charts.title') }}</h1>
    <div class="flex gap-1 text-sm">
        @foreach ([7 => '7 ' . __('app.charts.days'), 30 => '30 ' . __('app.charts.days'), 90 => '90 ' . __('app.charts.days'), 365 => __('app.charts.year'), 0 => __('app.charts.all')] as $p => $label)
            <a href="{{ route('charts', ['period' => $p]) }}" data-loader
               class="px-3 py-1.5 rounded-lg {{ $period === $p ? 'bg-teal-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

@if (! $hasData)
    <div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
        <p class="text-sm text-slate-500">{{ __('app.charts.empty') }}</p>
    </div>
@else
    <div class="flex flex-wrap gap-1.5 mb-4" id="metric-buttons">
        @foreach (\App\Http\Controllers\ChartsController::METRICS as $metric)
            @if ($series[$metric]->isNotEmpty())
                <button type="button" data-metric="{{ $metric }}"
                        class="metric-btn px-3 py-1.5 rounded-lg text-sm border {{ $loop->first ? 'bg-teal-600 border-teal-600 text-white' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    {{ __('app.metrics.' . $metric) }}
                </button>
            @endif
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="relative h-80">
            <canvas id="metric-chart"></canvas>
        </div>
    </div>
@endif
@endsection

@push('scripts')
@if ($hasData)
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
    const series = @json($series);
    const labels = @json(collect(\App\Http\Controllers\ChartsController::METRICS)->mapWithKeys(fn ($m) => [$m => __('app.metrics.' . $m)]));

    let chart = null;

    function render(metric) {
        if (chart) chart.destroy();
        chart = new Chart(document.getElementById('metric-chart'), {
            type: 'line',
            data: {
                datasets: [{
                    label: labels[metric],
                    data: series[metric],
                    borderColor: '#0d9488',
                    backgroundColor: '#0d9488',
                    pointRadius: 3,
                    tension: 0.3,
                }],
            },
            options: {
                maintainAspectRatio: false,
                interaction: {mode: 'nearest', intersect: false},
                scales: {
                    x: {type: 'category', ticks: {maxTicksLimit: 10}, grid: {display: false}},
                    y: {grace: '10%'},
                },
                plugins: {legend: {display: false}},
            },
        });
    }

    const buttons = document.querySelectorAll('.metric-btn');
    buttons.forEach(btn => btn.addEventListener('click', () => {
        buttons.forEach(b => b.className = 'metric-btn px-3 py-1.5 rounded-lg text-sm border bg-white border-slate-200 text-slate-600 hover:bg-slate-50');
        btn.className = 'metric-btn px-3 py-1.5 rounded-lg text-sm border bg-teal-600 border-teal-600 text-white';
        render(btn.dataset.metric);
    }));

    if (buttons.length) render(buttons[0].dataset.metric);
</script>
@endif
@endpush
