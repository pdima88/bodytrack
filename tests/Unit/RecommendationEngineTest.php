<?php

namespace Tests\Unit;

use App\Models\Measurement;
use App\Models\Profile;
use App\Services\BodyNorms;
use App\Services\RecommendationEngine;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RecommendationEngineTest extends TestCase
{
    private RecommendationEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new RecommendationEngine(new BodyNorms());
    }

    private function profile(array $attrs = []): Profile
    {
        $profile = new Profile();
        $profile->sex = $attrs['sex'] ?? 'male';
        $profile->birth_date = now()->subYears($attrs['age'] ?? 38);
        $profile->height_cm = $attrs['height_cm'] ?? 178;
        $profile->activity_level = $attrs['activity_level'] ?? 'moderate';
        $profile->target_weight_kg = $attrs['target_weight_kg'] ?? null;

        return $profile;
    }

    private function measurement(array $attrs): Measurement
    {
        $m = new Measurement();
        foreach ($attrs as $k => $v) {
            $m->{$k} = $v;
        }

        return $m;
    }

    private function keys(array $items): array
    {
        return array_column($items, 'key');
    }

    public function test_all_good_when_everything_is_normal(): void
    {
        $history = new Collection([
            $this->measurement(['measured_at' => now()->subDays(10), 'weight_kg' => 75.5, 'fat_percent' => 15, 'water_percent' => 58, 'muscle_percent' => 36]),
            $this->measurement(['measured_at' => now(), 'weight_kg' => 75.2, 'fat_percent' => 15, 'water_percent' => 58, 'muscle_percent' => 36]),
        ]);

        $items = $this->engine->recommend($this->profile(), $history);

        $this->assertSame(['all_good'], $this->keys($items));
    }

    public function test_flags_high_fat_and_visceral(): void
    {
        $history = new Collection([
            $this->measurement(['measured_at' => now(), 'weight_kg' => 82.4, 'fat_percent' => 24.8, 'visceral_fat' => 11]),
        ]);

        $keys = $this->keys($this->engine->recommend($this->profile(), $history));

        $this->assertContains('fat_high', $keys);
        $this->assertContains('visceral_high', $keys);
    }

    public function test_good_pace_and_forecast_when_losing_towards_target(): void
    {
        $history = new Collection([
            $this->measurement(['measured_at' => now()->subDays(28), 'weight_kg' => 84.0]),
            $this->measurement(['measured_at' => now(), 'weight_kg' => 82.4]),
        ]);

        $keys = $this->keys($this->engine->recommend($this->profile(['target_weight_kg' => 78.0]), $history));

        $this->assertContains('losing_good_pace', $keys);
        $this->assertContains('target_forecast', $keys);
    }

    public function test_losing_too_fast_is_flagged(): void
    {
        $history = new Collection([
            $this->measurement(['measured_at' => now()->subDays(7), 'weight_kg' => 84.0]),
            $this->measurement(['measured_at' => now(), 'weight_kg' => 82.0]),
        ]);

        $keys = $this->keys($this->engine->recommend($this->profile(), $history));

        $this->assertContains('losing_too_fast', $keys);
    }

    public function test_gaining_with_lower_target_suggests_deficit(): void
    {
        $history = new Collection([
            $this->measurement(['measured_at' => now()->subDays(14), 'weight_kg' => 82.0]),
            $this->measurement(['measured_at' => now(), 'weight_kg' => 83.5]),
        ]);

        $items = $this->engine->recommend($this->profile(['target_weight_kg' => 78.0]), $history);
        $keys = $this->keys($items);

        $this->assertContains('gaining_but_target_lower', $keys);

        $rec = collect($items)->firstWhere('key', 'gaining_but_target_lower');
        $this->assertGreaterThan(1500, $rec['params']['tdee']);
        $this->assertSame($rec['params']['tdee'] - 400, $rec['params']['intake']);
    }

    public function test_low_water_recommends_fluids(): void
    {
        $history = new Collection([
            $this->measurement(['measured_at' => now(), 'weight_kg' => 80.0, 'water_percent' => 45.0]),
        ]);

        $keys = $this->keys($this->engine->recommend($this->profile(), $history));

        $this->assertContains('water_low', $keys);
    }

    public function test_stale_measurement_reminds_to_weigh(): void
    {
        $history = new Collection([
            $this->measurement(['measured_at' => now()->subDays(12), 'weight_kg' => 75.5, 'fat_percent' => 15, 'water_percent' => 58, 'muscle_percent' => 36]),
        ]);

        $keys = $this->keys($this->engine->recommend($this->profile(), $history));

        $this->assertContains('measure_regularly', $keys);
    }

    public function test_warnings_come_before_good_news(): void
    {
        $history = new Collection([
            $this->measurement(['measured_at' => now()->subDays(28), 'weight_kg' => 84.0, 'fat_percent' => 26.0]),
            $this->measurement(['measured_at' => now(), 'weight_kg' => 82.4, 'fat_percent' => 24.8]),
        ]);

        $items = $this->engine->recommend($this->profile(['target_weight_kg' => 78.0]), $history);

        $severities = array_column($items, 'severity');
        $this->assertSame($severities, collect($severities)->sortBy(fn ($s) => ['warning' => 0, 'info' => 1, 'good' => 2][$s])->values()->all());
        $this->assertContains('fat_falling', $this->keys($items));
    }
}
