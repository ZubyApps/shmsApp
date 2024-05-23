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
        Schema::create('shift_performances', function (Blueprint $table) {
            $table->id();
            $table->string('shift');
            $table->string('department');
            $table->string('billing_res')->nullable();
            $table->string('inpatient_dispense_res')->nullable();
            $table->string('chart_rate')->nullable();
            $table->string('first_med_res')->nullable();
            $table->string('first_vitals_res')->nullable();
            $table->string('medication_time')->nullable();
            $table->string('inpatient_vitals_count')->nullable();
            $table->string('outpatient_vitals_count')->nullable();
            $table->float('performance')->default(0);
            $table->json('staff')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_performances');
    }
};
