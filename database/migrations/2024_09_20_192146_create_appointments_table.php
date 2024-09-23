<?php

use App\Models\Patient;
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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->text('remarks')->nullable();
            $table->foreignIdFor(Patient::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class, 'doctor_id')->constrained('users')->restrictOnDelete();
            // $table->foreignIdFor(Visit::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
