<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sex' => fake()->randomElement(Profile::SEXES),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'height_cm' => fake()->numberBetween(150, 200),
            'activity_level' => fake()->randomElement(Profile::ACTIVITY_LEVELS),
            'target_weight_kg' => null,
        ];
    }
}
