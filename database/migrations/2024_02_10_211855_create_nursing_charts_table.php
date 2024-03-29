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
        Schema::create('nursing_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Prescription::class)->constrained()->cascadeOnDelete();
            $table->string('care_prescribed');
            $table->string('scheduled_time');
            $table->string('not_done')->nullable();
            $table->dateTime('time_done')->nullable();
            $table->string('note')->nullable();
            $table->foreignIdFor(User::class, 'done_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->boolean('status')->default(false);
            $table->integer('schedule_count');
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Consultation::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nursing_charts');
    }
};
