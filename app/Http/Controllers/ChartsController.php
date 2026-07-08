<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ChartsController extends Controller
{
    public const PERIODS = [7, 30, 90, 365, 0];

    public const METRICS = [
        'weight_kg',
        'fat_percent',
        'water_percent',
        'muscle_percent',
        'bone_kg',
        'visceral_fat',
        'bmi',
        'bmr_kcal',
    ];

    public function index(Request $request): View
    {
        $period = (int) $request->query('period', 30);
        if (! in_array($period, self::PERIODS, true)) {
            $period = 30;
        }

        $query = $request->user()->measurements()->orderBy('measured_at');

        if ($period > 0) {
            $query->where('measured_at', '>=', now()->subDays($period));
        }

        $measurements = $query->get();

        $series = [];
        foreach (self::METRICS as $metric) {
            $series[$metric] = $measurements
                ->filter(fn ($m) => $m->{$metric} !== null)
                ->map(fn ($m) => [
                    'x' => $m->measured_at->format('Y-m-d H:i'),
                    'y' => $m->{$metric},
                ])
                ->values();
        }

        return view('charts.index', [
            'series' => $series,
            'period' => $period,
            'hasData' => $measurements->isNotEmpty(),
        ]);
    }
}
