<?php

namespace Tests\Unit;

use App\Models\Measurement;
use App\Models\Profile;
use App\Services\BodyNorms;
use Tests\TestCase;

class BodyNormsTest extends TestCase
{
    private BodyNorms $norms;

    protected function setUp(): void
    {
        parent::setUp();
        $this->norms = new BodyNorms();
    }

    private function profile(string $sex = 'male', int $age = 38, int $height = 178): Profile
    {
        $profile = new Profile();
        $profile->sex = $sex;
        $profile->birth_date = now()->subYears($age)->subMonth();
        $profile->height_cm = $height;

        return $profile;
    }

    public function test_bmi_calculation_and_categories(): void
    {
        $profile = $this->profile();

        $this->assertSame(26.0, $this->norms->bmi($profile, 82.4));
        $this->assertSame('underweight', $this->norms->bmiCategory(18.0));
        $this->assertSame('normal', $this->norms->bmiCategory(22.0));
        $this->assertSame('overweight', $this->norms->bmiCategory(26.0));
        $this->assertSame('obese', $this->norms->bmiCategory(31.0));
    }

    public function test_mifflin_bmr(): void
    {
        $male = $this->norms->mifflinBmr($this->profile('male', 38, 178), 82.4);
        $this->assertSame((int) round(10 * 82.4 + 6.25 * 178 - 5 * 38 + 5), $male);

        $female = $this->norms->mifflinBmr($this->profile('female', 38, 178), 82.4);
        $this->assertSame($male - 166, $female);
    }

    public function test_fat_range_depends_on_sex_and_age(): void
    {
        $this->assertSame([8.0, 20.0], $this->norms->fatRange($this->profile('male', 30)));
        $this->assertSame([11.0, 22.0], $this->norms->fatRange($this->profile('male', 50)));
        $this->assertSame([13.0, 25.0], $this->norms->fatRange($this->profile('male', 65)));
        $this->assertSame([21.0, 33.0], $this->norms->fatRange($this->profile('female', 30)));
    }

    public function test_evaluate_flags_deviations(): void
    {
        $profile = $this->profile('male', 38, 178);

        $m = new Measurement();
        $m->weight_kg = 82.4;
        $m->fat_percent = 24.8;
        $m->water_percent = 51.2;
        $m->muscle_percent = 33.1;
        $m->bone_percent = 3.9;
        $m->visceral_fat = 11;
        $m->bmi = 26.1;
        $m->bmr_kcal = 1740;

        $result = $this->norms->evaluate($profile, $m);

        $this->assertSame(BodyNorms::STATUS_HIGH, $result['bmi']['status']);
        $this->assertSame(BodyNorms::STATUS_HIGH, $result['fat_percent']['status']);
        $this->assertSame(BodyNorms::STATUS_NORMAL, $result['water_percent']['status']);
        $this->assertSame(BodyNorms::STATUS_LOW, $result['muscle_percent']['status']);
        $this->assertSame(BodyNorms::STATUS_NORMAL, $result['bone_percent']['status']);
        $this->assertSame(BodyNorms::STATUS_HIGH, $result['visceral_fat']['status']);
        $this->assertSame(BodyNorms::STATUS_INFO, $result['bmr_kcal']['status']);
    }

    public function test_visceral_fat_scale(): void
    {
        $profile = $this->profile();

        foreach ([5 => BodyNorms::STATUS_NORMAL, 12 => BodyNorms::STATUS_HIGH, 16 => BodyNorms::STATUS_VERY_HIGH] as $value => $expected) {
            $m = new Measurement();
            $m->weight_kg = 80;
            $m->visceral_fat = $value;

            $this->assertSame($expected, $this->norms->evaluate($profile, $m)['visceral_fat']['status']);
        }
    }
}
