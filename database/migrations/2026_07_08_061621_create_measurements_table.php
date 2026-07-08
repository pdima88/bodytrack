<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('measured_at');
            $table->decimal('weight_kg', 5, 1);
            $table->decimal('fat_percent', 4, 1)->nullable();
            $table->decimal('water_percent', 4, 1)->nullable();
            $table->decimal('muscle_percent', 4, 1)->nullable();
            $table->decimal('bone_kg', 4, 1)->nullable();
            $table->unsignedTinyInteger('visceral_fat')->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->unsignedSmallInteger('bmr_kcal')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
