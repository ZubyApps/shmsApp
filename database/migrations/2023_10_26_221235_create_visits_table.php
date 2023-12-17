<?php

use App\Models\Patient;
use App\Models\Sponsor;
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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('verification_status')->default(false);
            $table->string('verification_code')->nullable();
            $table->dateTime('consulted')->nullable();
            $table->boolean('closed')->default(false);
            $table->string('doctor_done')->nullable();
            $table->string('nurse_done')->nullable();
            $table->string('pharmacy_done')->nullable();
            $table->string('lab_done')->nullable();
            $table->string('billing_done')->nullable();
            $table->string('hmo_done')->nullable();
            $table->foreignIdFor(User::class, 'doctor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(Patient::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Sponsor::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
