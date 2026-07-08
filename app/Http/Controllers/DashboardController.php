<?php

namespace App\Http\Controllers;

use App\Services\BodyNorms;
use App\Services\RecommendationEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly BodyNorms $norms,
        private readonly RecommendationEngine $engine,
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $latest = $user->measurements()->orderByDesc('measured_at')->first();

        if ($latest === null) {
            return view('dashboard', ['latest' => null]);
        }

        $profile = $user->profile;

        $history = $user->measurements()
            ->where('measured_at', '>=', $latest->measured_at->copy()->subDays(30))
            ->orderBy('measured_at')
            ->get();

        return view('dashboard', [
            'latest' => $latest,
            'profile' => $profile,
            'metrics' => $this->norms->evaluate($profile, $latest),
            'weekDeltas' => $this->weekDeltas($user->measurements()->orderBy('measured_at')->get(), $latest),
            'chart' => $this->weightChart($history, $profile->target_weight_kg),
            'recommendations' => $this->engine->recommend($profile, $history),
        ]);
    }

    /**
     * Change of key metrics against the closest measurement at least 7 days
     * older than the latest one (or the oldest available if history is short).
     */
    private function weekDeltas(Collection $all, $latest): array
    {
        $reference = $all
            ->filter(fn ($m) => $m->measured_at->lte($latest->measured_at->copy()->subDays(7)))
            ->last() ?? $all->first();

        if ($reference === null || $reference->id === $latest->id) {
            return [];
        }

        $deltas = [];

        foreach (['weight_kg', 'fat_percent', 'muscle_percent'] as $field) {
            if ($latest->{$field} !== null && $reference->{$field} !== null) {
                $deltas[$field] = round($latest->{$field} - $reference->{$field}, 1);
            }
        }

        return $deltas;
    }

    private function weightChart(Collection $history, ?float $target): array
    {
        $points = $history->map(fn ($m) => [
            'x' => $m->measured_at->format('Y-m-d H:i'),
            'y' => $m->weight_kg,
        ])->values();

        $movingAverage = [];
        foreach ($history as $m) {
            $window = $history->filter(
                fn ($other) => $other->measured_at->between(
                    $m->measured_at->copy()->subDays(7),
                    $m->measured_at
                )
            );
            $movingAverage[] = [
                'x' => $m->measured_at->format('Y-m-d H:i'),
                'y' => round($window->avg('weight_kg'), 1),
            ];
        }

        return [
            'points' => $points,
            'movingAverage' => $movingAverage,
            'target' => $target,
        ];
    }
}
