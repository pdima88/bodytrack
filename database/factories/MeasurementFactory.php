<?php

namespace Database\Factories;

use App\Models\Measurement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Measurement>
 */
class MeasurementFactory extends Factory
{
    public function definition(): array
    {
        $weight = fake()->randomFloat(1, 60, 100);

        return [
            'measured_at' => fake()->dateTimeBetween('-3 months'),
            'weight_kg' => $weight,
            'fat_percent' => fake()->randomFloat(1, 12, 35),
            'water_percent' => fake()->randomFloat(1, 45, 65),
            'muscle_percent' => fake()->randomFloat(1, 25, 45),
            'bone_percent' => fake()->randomFloat(1, 3, 5),
            'visceral_fat' => fake()->numberBetween(3, 15),
            'bmi' => round($weight / (1.78 ** 2), 1),
            'bmr_kcal' => fake()->numberBetween(1300, 2200),
        ];
    }
}
