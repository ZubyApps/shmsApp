<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->after('mid_arm_circumference', function (Blueprint $table) {
                $table->string('fluid_drain')->nullable();
                $table->string('urine_output')->nullable();
                $table->string('fetal_heart_rate')->nullable();

            });

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->dropColumn(['fluid_drain', 'urine_output', 'fetal_heart_rate']);
        });
    }
};
