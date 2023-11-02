<?php

use App\Models\Patient;
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
            $table->boolean('verification_status')->default(false);
            $table->string('verification_code')->nullable();
            $table->dateTime('consulted')->nullable();
            $table->boolean('closed')->default(false);
            $table->foreignIdFor(User::class, 'doctor_id')->nullable();
            $table->dateTime('vital_signs')->nullable();
            $table->foreignIdFor(Patient::class);
            $table->foreignIdFor(User::class);
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
