<?php

namespace App\Services;

use App\Models\Measurement;
use App\Models\Profile;
use Illuminate\Support\Collection;

/**
 * Rule-based practical recommendations built from the latest measurement,
 * its norm evaluation and 4-week trends. Returns a list of
 * ['key' => translation key under app.recommendations, 'params' => [...],
 *  'severity' => good|info|warning] sorted by severity.
 */
class RecommendationEngine
{
    /** Multipliers for total daily energy expenditure by activity level. */
    private const ACTIVITY_FACTORS = [
        'sedentary' => 1.2,
        'light' => 1.375,
        'moderate' => 1.55,
        'high' => 1.725,
        'athlete' => 1.9,
    ];

    public function __construct(private readonly BodyNorms $norms)
    {
    }

    /**
     * @param Collection<int, Measurement> $history measurements of the last
     *        ~4 weeks, ordered by measured_at ascending, including the latest
     */
    public function recommend(Profile $profile, Collection $history): array
    {
        $latest = $history->last();

        if ($latest === null) {
            return [];
        }

        $metrics = $this->norms->evaluate($profile, $latest);
        $items = [];

        $this->addWeightPaceRules($items, $profile, $history, $latest, $metrics);
        $this->addFatRules($items, $profile, $history, $latest, $metrics);
        $this->addMuscleRules($items, $history, $metrics);
        $this->addWaterRules($items, $profile, $history, $latest, $metrics);
        $this->addVisceralRules($items, $metrics);
        $this->addRegularityRule($items, $latest);

        if ($items === []) {
            $items[] = ['key' => 'all_good', 'params' => [], 'severity' => 'good'];
        }

        $order = ['warning' => 0, 'info' => 1, 'good' => 2];
        usort($items, fn ($a, $b) => $order[$a['severity']] <=> $order[$b['severity']]);

        return $items;
    }

    /**
     * Average weight change per week over the observed period, in kg.
     * Needs at least ~a week of data to be meaningful.
     */
    public function weeklyWeightPace(Collection $history): ?float
    {
        if ($history->count() < 2) {
            return null;
        }

        $first = $history->first();
        $last = $history->last();
        $days = $first->measured_at->diffInDays($last->measured_at);

        if ($days < 5) {
            return null;
        }

        return round(($last->weight_kg - $first->weight_kg) / $days * 7, 2);
    }

    public function estimatedDailyExpenditure(Profile $profile, Measurement $latest): int
    {
        $bmr = $latest->bmr_kcal ?? $this->norms->mifflinBmr($profile, $latest->weight_kg);

        return (int) round($bmr * (self::ACTIVITY_FACTORS[$profile->activity_level] ?? 1.55));
    }

    private function addWeightPaceRules(array &$items, Profile $profile, Collection $history, Measurement $latest, array $metrics): void
    {
        $pace = $this->weeklyWeightPace($history);
        $target = $profile->target_weight_kg;
        $wantsToLose = $target !== null && $target < $latest->weight_kg;

        if ($pace !== null && $pace < 0 && abs($pace) > $latest->weight_kg * 0.01) {
            $items[] = [
                'key' => 'losing_too_fast',
                'params' => ['pace' => number_format(abs($pace), 1, ',', ' ')],
                'severity' => 'warning',
            ];
        } elseif ($pace !== null && $pace < -0.1 && $wantsToLose) {
            $items[] = [
                'key' => 'losing_good_pace',
                'params' => ['pace' => number_format(abs($pace), 1, ',', ' ')],
                'severity' => 'good',
            ];
        } elseif ($pace !== null && $pace >= 0.1 && $wantsToLose) {
            $tdee = $this->estimatedDailyExpenditure($profile, $latest);
            $items[] = [
                'key' => 'gaining_but_target_lower',
                'params' => ['tdee' => $tdee, 'intake' => $tdee - 400],
                'severity' => 'warning',
            ];
        }

        if ($wantsToLose && $pace !== null && $pace < -0.1) {
            $weeksLeft = (int) ceil(($latest->weight_kg - $target) / abs($pace));
            if ($weeksLeft > 0 && $weeksLeft < 200) {
                $items[] = [
                    'key' => 'target_forecast',
                    'params' => [
                        'remaining' => number_format($latest->weight_kg - $target, 1, ',', ' '),
                        'date' => now()->addWeeks($weeksLeft)->isoFormat('MMMM YYYY'),
                    ],
                    'severity' => 'info',
                ];
            }
        }

        if (($metrics['bmi']['value'] ?? 99) < 18.5) {
            $tdee = $this->estimatedDailyExpenditure($profile, $latest);
            $items[] = [
                'key' => 'underweight',
                'params' => ['intake' => $tdee + 300],
                'severity' => 'warning',
            ];
        }
    }

    private function addFatRules(array &$items, Profile $profile, Collection $history, Measurement $latest, array $metrics): void
    {
        if (! isset($metrics['fat_percent'])) {
            return;
        }

        $status = $metrics['fat_percent']['status'];

        $fatSeries = $history->filter(fn ($m) => $m->fat_percent !== null);
        $fatTrend = $fatSeries->count() >= 2
            ? round($fatSeries->last()->fat_percent - $fatSeries->first()->fat_percent, 1)
            : null;

        if ($status === BodyNorms::STATUS_HIGH) {
            $tdee = $this->estimatedDailyExpenditure($profile, $latest);
            $protein = (int) round($latest->weight_kg * 1.8);
            $items[] = [
                'key' => 'fat_high',
                'params' => ['max' => number_format($metrics['fat_percent']['range'][1], 1, ',', ' '), 'intake' => $tdee - 400, 'protein' => $protein],
                'severity' => 'warning',
            ];
        }

        if ($fatTrend !== null && $fatTrend <= -0.5) {
            $items[] = [
                'key' => 'fat_falling',
                'params' => ['delta' => number_format(abs($fatTrend), 1, ',', ' ')],
                'severity' => 'good',
            ];
        }
    }

    private function addMuscleRules(array &$items, Collection $history, array $metrics): void
    {
        if (! isset($metrics['muscle_percent'])) {
            return;
        }

        $muscleSeries = $history->filter(fn ($m) => $m->muscle_percent !== null);
        $muscleTrend = $muscleSeries->count() >= 2
            ? round($muscleSeries->last()->muscle_percent - $muscleSeries->first()->muscle_percent, 1)
            : null;

        $weightFalling = ($this->weeklyWeightPace($history) ?? 0) < -0.1;

        if ($metrics['muscle_percent']['status'] === BodyNorms::STATUS_LOW
            || ($muscleTrend !== null && $muscleTrend <= -0.5 && $weightFalling)) {
            $items[] = [
                'key' => 'muscle_low',
                'params' => [],
                'severity' => 'warning',
            ];
        }
    }

    private function addWaterRules(array &$items, Profile $profile, Collection $history, Measurement $latest, array $metrics): void
    {
        if (! isset($metrics['water_percent'])) {
            return;
        }

        $recentLow = $history->filter(fn ($m) => $m->water_percent !== null)
            ->reverse()
            ->take(3)
            ->every(fn ($m) => $m->water_percent < $metrics['water_percent']['range'][0]);

        if ($metrics['water_percent']['status'] === BodyNorms::STATUS_LOW || ($recentLow && $history->count() >= 3)) {
            $liters = number_format(round($latest->weight_kg * 0.033, 1), 1, ',', ' ');
            $items[] = [
                'key' => 'water_low',
                'params' => ['liters' => $liters],
                'severity' => 'warning',
            ];
        }
    }

    private function addVisceralRules(array &$items, array $metrics): void
    {
        if (! isset($metrics['visceral_fat'])) {
            return;
        }

        if ($metrics['visceral_fat']['status'] === BodyNorms::STATUS_HIGH) {
            $items[] = ['key' => 'visceral_high', 'params' => [], 'severity' => 'warning'];
        } elseif ($metrics['visceral_fat']['status'] === BodyNorms::STATUS_VERY_HIGH) {
            $items[] = ['key' => 'visceral_very_high', 'params' => [], 'severity' => 'warning'];
        }
    }

    private function addRegularityRule(array &$items, Measurement $latest): void
    {
        if ($latest->measured_at->lt(now()->subDays(7))) {
            $items[] = ['key' => 'measure_regularly', 'params' => [], 'severity' => 'info'];
        }
    }
}
