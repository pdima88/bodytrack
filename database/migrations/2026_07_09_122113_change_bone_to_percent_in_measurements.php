<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Geepas scale reports bone mass as a percentage of body weight,
     * not in kilograms. Rename the column and convert existing values.
     */
    public function up(): void
    {
        Schema::table('measurements', function (Blueprint $table) {
            $table->renameColumn('bone_kg', 'bone_percent');
        });

        DB::table('measurements')
            ->whereNotNull('bone_percent')
            ->update(['bone_percent' => DB::raw('round(bone_percent / weight_kg * 100, 1)')]);
    }

    public function down(): void
    {
        DB::table('measurements')
            ->whereNotNull('bone_percent')
            ->update(['bone_percent' => DB::raw('round(bone_percent * weight_kg / 100, 1)')]);

        Schema::table('measurements', function (Blueprint $table) {
            $table->renameColumn('bone_percent', 'bone_kg');
        });
    }
};
