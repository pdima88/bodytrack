<?php

namespace App\Services;

use App\Models\Measurement;
use App\Models\Profile;

/**
 * Reference ranges for body composition metrics.
 *
 * Sources: WHO BMI classification; commonly used BIA reference tables
 * (Gallagher et al. for fat %, Tanita for water/skeletal muscle %),
 * Mifflin-St Jeor for basal metabolic rate. Ranges depend on sex and age.
 */
class BodyNorms
{
    public const STATUS_LOW = 'low';
    public const STATUS_NORMAL = 'normal';
    public const STATUS_HIGH = 'high';
    public const STATUS_VERY_HIGH = 'very_high';
    public const STATUS_INFO = 'info';

    /**
     * Evaluate every present metric of a measurement against the profile's
     * norms. Returns [metric => ['value', 'status', 'range' => [min, max]]].
     */
    public function evaluate(Profile $profile, Measurement $m): array
    {
        $result = [];

        $bmi = $m->bmi ?? $this->bmi($profile, $m->weight_kg);
        $result['bmi'] = [
            'value' => $bmi,
            'status' => $this->statusFromRange($bmi, 18.5, 25.0),
            'range' => [18.5, 25.0],
        ];

        if ($m->fat_percent !== null) {
            [$min, $max] = $this->fatRange($profile);
            $result['fat_percent'] = [
                'value' => $m->fat_percent,
                'status' => $this->statusFromRange($m->fat_percent, $min, $max),
                'range' => [$min, $max],
            ];
        }

        if ($m->water_percent !== null) {
            [$min, $max] = $profile->sex === 'male' ? [50.0, 65.0] : [45.0, 60.0];
            $result['water_percent'] = [
                'value' => $m->water_percent,
                'status' => $this->statusFromRange($m->water_percent, $min, $max),
                'range' => [$min, $max],
            ];
        }

        if ($m->muscle_percent !== null) {
            [$min, $max] = $this->muscleRange($profile);
            $result['muscle_percent'] = [
                'value' => $m->muscle_percent,
                'status' => $this->statusFromRange($m->muscle_percent, $min, $max),
                'range' => [$min, $max],
            ];
        }

        if ($m->bone_percent !== null) {
            $reference = round($this->boneReference($profile->sex, $m->weight_kg) / $m->weight_kg * 100, 1);
            $result['bone_percent'] = [
                'value' => $m->bone_percent,
                'status' => $this->statusFromRange($m->bone_percent, round($reference * 0.8, 1), round($reference * 1.2, 1)),
                'range' => [round($reference * 0.8, 1), round($reference * 1.2, 1)],
            ];
        }

        if ($m->visceral_fat !== null) {
            $result['visceral_fat'] = [
                'value' => $m->visceral_fat,
                'status' => match (true) {
                    $m->visceral_fat <= 9 => self::STATUS_NORMAL,
                    $m->visceral_fat <= 14 => self::STATUS_HIGH,
                    default => self::STATUS_VERY_HIGH,
                },
                'range' => [1, 9],
            ];
        }

        if ($m->bmr_kcal !== null) {
            $result['bmr_kcal'] = [
                'value' => $m->bmr_kcal,
                'status' => self::STATUS_INFO,
                'range' => [$this->mifflinBmr($profile, $m->weight_kg), null],
            ];
        }

        return $result;
    }

    public function bmi(Profile $profile, float $weightKg): float
    {
        $heightM = $profile->height_cm / 100;

        return round($weightKg / ($heightM ** 2), 1);
    }

    public function bmiCategory(float $bmi): string
    {
        return match (true) {
            $bmi < 18.5 => 'underweight',
            $bmi < 25.0 => 'normal',
            $bmi < 30.0 => 'overweight',
            default => 'obese',
        };
    }

    /**
     * Estimated basal metabolic rate (Mifflin-St Jeor), kcal/day.
     */
    public function mifflinBmr(Profile $profile, float $weightKg): int
    {
        $base = 10 * $weightKg + 6.25 * $profile->height_cm - 5 * $profile->age();

        return (int) round($profile->sex === 'male' ? $base + 5 : $base - 161);
    }

    /**
     * Body fat % healthy range by sex and age (Gallagher et al. 2000).
     *
     * @return array{0: float, 1: float}
     */
    public function fatRange(Profile $profile): array
    {
        $age = $profile->age();

        if ($profile->sex === 'male') {
            return match (true) {
                $age < 40 => [8.0, 20.0],
                $age < 60 => [11.0, 22.0],
                default => [13.0, 25.0],
            };
        }

        return match (true) {
            $age < 40 => [21.0, 33.0],
            $age < 60 => [23.0, 34.0],
            default => [24.0, 36.0],
        };
    }

    /**
     * Skeletal muscle % reference (Tanita BIA tables).
     *
     * @return array{0: float, 1: float}
     */
    public function muscleRange(Profile $profile): array
    {
        $age = $profile->age();

        if ($profile->sex === 'male') {
            return match (true) {
                $age < 40 => [33.3, 39.3],
                $age < 60 => [33.1, 39.1],
                default => [32.9, 38.9],
            };
        }

        return match (true) {
            $age < 40 => [24.3, 30.3],
            $age < 60 => [24.1, 30.1],
            default => [23.9, 29.9],
        };
    }

    /**
     * Expected bone mass in kg for the given sex and body weight (Tanita).
     */
    public function boneReference(string $sex, float $weightKg): float
    {
        if ($sex === 'male') {
            return match (true) {
                $weightKg < 65 => 2.65,
                $weightKg <= 95 => 3.29,
                default => 3.69,
            };
        }

        return match (true) {
            $weightKg < 50 => 1.95,
            $weightKg <= 75 => 2.40,
            default => 2.95,
        };
    }

    private function statusFromRange(float $value, float $min, float $max): string
    {
        return match (true) {
            $value < $min => self::STATUS_LOW,
            $value > $max => self::STATUS_HIGH,
            default => self::STATUS_NORMAL,
        };
    }
}
