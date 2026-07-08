<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sex', 'birth_date', 'height_cm', 'activity_level', 'target_weight_kg'])]
class Profile extends Model
{
    use HasFactory;

    public const SEXES = ['male', 'female'];

    public const ACTIVITY_LEVELS = ['sedentary', 'light', 'moderate', 'high', 'athlete'];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'height_cm' => 'integer',
            'target_weight_kg' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function age(): int
    {
        return (int) $this->birth_date->age;
    }
}
