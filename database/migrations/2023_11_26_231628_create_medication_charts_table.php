<?php

use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\User;
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
        Schema::create('medication_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Prescription::class);
            $table->string('dose_prescribed');
            $table->string('scheduled_time');
            $table->string('dose_given')->nullable();
            $table->dateTime('time_given')->nullable();
            $table->string('note')->nullable();
            $table->foreignIdFor(User::class, 'given_by')->nullable();
            $table->boolean('status')->default(false);
            $table->integer('dose_count');
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Consultation::class);
            $table->foreignIdFor(Visit::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_charts');
    }
};
