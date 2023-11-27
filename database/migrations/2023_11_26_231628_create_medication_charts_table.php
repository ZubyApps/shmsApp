<?php

use App\Models\Prescription;
use App\Models\User;
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
            $table->string('dose_given')->nullable();
            $table->dateTime('time_given')->nullable();
            $table->foreignIdFor(User::class, 'giver_id')->nullable();
            $table->foreignIdFor(User::class);
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
