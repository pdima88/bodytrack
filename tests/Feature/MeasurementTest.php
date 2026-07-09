<?php

namespace Tests\Feature;

use App\Models\Measurement;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeasurementTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->has(Profile::factory())->create();
    }

    public function test_user_can_store_measurement_with_all_fields(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post('/measurements', [
            'measured_at' => now()->format('Y-m-d\TH:i'),
            'weight_kg' => 82.4,
            'fat_percent' => 24.8,
            'water_percent' => 51.2,
            'muscle_percent' => 33.1,
            'bone_percent' => 3.9,
            'visceral_fat' => 11,
            'bmi' => 26.1,
            'bmr_kcal' => 1740,
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('measurements', [
            'user_id' => $user->id,
            'weight_kg' => 82.4,
            'visceral_fat' => 11,
        ]);
    }

    public function test_weight_only_measurement_is_allowed(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post('/measurements', [
            'measured_at' => now()->format('Y-m-d\TH:i'),
            'weight_kg' => 80,
        ])->assertRedirect('/dashboard');

        $this->assertSame(1, $user->measurements()->count());
    }

    public function test_big_weight_jump_requires_confirmation(): void
    {
        $user = $this->user();
        Measurement::factory()->for($user)->create(['weight_kg' => 80, 'measured_at' => now()->subDay()]);

        $payload = [
            'measured_at' => now()->format('Y-m-d\TH:i'),
            'weight_kg' => 90,
        ];

        $this->actingAs($user)->post('/measurements', $payload)->assertSessionHas('anomaly');
        $this->assertSame(1, $user->measurements()->count());

        $this->actingAs($user)->post('/measurements', $payload + ['confirm_anomaly' => 1])
            ->assertRedirect('/dashboard');
        $this->assertSame(2, $user->measurements()->count());
    }

    public function test_validation_rejects_out_of_range_values(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post('/measurements', [
            'measured_at' => now()->addDay()->format('Y-m-d\TH:i'),
            'weight_kg' => 250,
            'fat_percent' => 90,
            'visceral_fat' => 99,
        ])->assertSessionHasErrors(['measured_at', 'weight_kg', 'fat_percent', 'visceral_fat']);
    }

    public function test_user_can_update_and_delete_own_measurement(): void
    {
        $user = $this->user();
        $m = Measurement::factory()->for($user)->create();

        $this->actingAs($user)->put("/measurements/{$m->id}", [
            'measured_at' => now()->format('Y-m-d\TH:i'),
            'weight_kg' => 77.7,
        ])->assertRedirect('/measurements');

        $this->assertSame(77.7, $m->fresh()->weight_kg);

        $this->actingAs($user)->delete("/measurements/{$m->id}")->assertRedirect('/measurements');
        $this->assertDatabaseMissing('measurements', ['id' => $m->id]);
    }

    public function test_user_cannot_touch_foreign_measurement(): void
    {
        $owner = $this->user();
        $intruder = $this->user();
        $m = Measurement::factory()->for($owner)->create();

        $this->actingAs($intruder)->get("/measurements/{$m->id}/edit")->assertNotFound();
        $this->actingAs($intruder)->delete("/measurements/{$m->id}")->assertNotFound();
        $this->assertDatabaseHas('measurements', ['id' => $m->id]);
    }

    public function test_history_page_shows_measurements(): void
    {
        $user = $this->user();
        Measurement::factory()->for($user)->create(['weight_kg' => 82.4]);

        $this->actingAs($user)->get('/measurements')
            ->assertOk()
            ->assertSee('82,4');
    }
}
