<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_profile_is_redirected_to_profile_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect('/profile');
    }

    public function test_user_can_fill_profile_and_reach_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'sex' => 'male',
            'birth_date' => '1988-05-14',
            'height_cm' => 178,
            'activity_level' => 'moderate',
            'target_weight_kg' => 78,
        ]);

        $response->assertRedirect('/dashboard');

        $profile = $user->fresh()->profile;
        $this->assertNotNull($profile);
        $this->assertSame('male', $profile->sex);
        $this->assertSame(178, $profile->height_cm);
        $this->assertSame(78.0, $profile->target_weight_kg);

        $this->actingAs($user->fresh())->get('/dashboard')->assertOk();
    }

    public function test_profile_validation_rejects_bad_values(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/profile', [
            'sex' => 'other',
            'birth_date' => '2030-01-01',
            'height_cm' => 90,
            'activity_level' => 'extreme',
            'target_weight_kg' => 5,
        ])->assertSessionHasErrors(['sex', 'birth_date', 'height_cm', 'activity_level', 'target_weight_kg']);
    }

    public function test_existing_profile_can_be_updated(): void
    {
        $user = User::factory()->has(Profile::factory())->create();

        $this->actingAs($user)->put('/profile', [
            'sex' => 'female',
            'birth_date' => '1990-01-01',
            'height_cm' => 165,
            'activity_level' => 'light',
            'target_weight_kg' => 60,
        ])->assertRedirect('/profile');

        $this->assertSame(165, $user->fresh()->profile->height_cm);
        $this->assertDatabaseCount('profiles', 1);
    }
}
