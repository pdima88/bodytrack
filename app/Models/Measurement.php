<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'measured_at',
    'weight_kg',
    'fat_percent',
    'water_percent',
    'muscle_percent',
    'bone_kg',
    'visceral_fat',
    'bmi',
    'bmr_kcal',
])]
class Measurement extends Model
{
    /** @use HasFactory<\Database\Factories\MeasurementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'measured_at' => 'datetime',
            'weight_kg' => 'float',
            'fat_percent' => 'float',
            'water_percent' => 'float',
            'muscle_percent' => 'float',
            'bone_kg' => 'float',
            'visceral_fat' => 'integer',
            'bmi' => 'float',
            'bmr_kcal' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
