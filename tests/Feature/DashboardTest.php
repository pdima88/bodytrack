<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_metrics_with_statuses(): void
    {
        $user = User::factory()
            ->has(Profile::factory()->state([
                'sex' => 'male',
                'birth_date' => now()->subYears(38),
                'height_cm' => 178,
                'target_weight_kg' => 78,
            ]))
            ->create();

        Measurement::factory()->for($user)->create([
            'measured_at' => now(),
            'weight_kg' => 82.4,
            'fat_percent' => 24.8,
            'water_percent' => 51.2,
            'muscle_percent' => 33.1,
            'bone_percent' => 3.9,
            'visceral_fat' => 11,
            'bmi' => 26.1,
            'bmr_kcal' => 1740,
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('82,4')
            ->assertSee('выше нормы')
            ->assertSee('норма')
            ->assertSee('избыточный вес')
            ->assertSee('Индекс массы тела');
    }

    public function test_dashboard_shows_week_delta(): void
    {
        $user = User::factory()->has(Profile::factory())->create();

        Measurement::factory()->for($user)->create([
            'measured_at' => now()->subDays(8),
            'weight_kg' => 83.0,
        ]);
        Measurement::factory()->for($user)->create([
            'measured_at' => now(),
            'weight_kg' => 82.4,
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('-0,6');
    }

    public function test_charts_page_renders_with_period_filter(): void
    {
        $user = User::factory()->has(Profile::factory())->create();
        Measurement::factory()->for($user)->count(3)->create();

        $this->actingAs($user)->get('/charts?period=90')->assertOk();
        $this->actingAs($user)->get('/charts?period=999')->assertOk();
    }
}
