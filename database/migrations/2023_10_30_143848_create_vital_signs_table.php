<?php

use App\Models\Visit;
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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->string('temperature')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->string('respiratory_rate')->nullable();
            $table->string('pulse_rate')->nullable();
            $table->string('sugar-level')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->foreignIdFor(Visit::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
