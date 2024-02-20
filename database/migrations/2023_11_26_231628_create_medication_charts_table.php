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
            $table->foreignIdFor(Prescription::class)->constrained()->cascadeOnDelete();
            $table->string('dose_prescribed');
            $table->string('scheduled_time');
            $table->string('dose_given')->nullable();
            $table->string('not_given')->nullable();
            $table->dateTime('time_given')->nullable();
            $table->string('note')->nullable();
            $table->foreignIdFor(User::class, 'given_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->boolean('status')->default(false);
            $table->integer('dose_count');
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Consultation::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();
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
