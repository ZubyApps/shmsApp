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
        Schema::create('medical_reports', function (Blueprint $table) {
            $table->id();
            $table->string('doctor');
            $table->string('designation');
            $table->string('type');
            $table->string('requested_by');
            $table->text('recipients_address')->nullable();
            $table->text('report');
            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Patient::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_reports');
    }
};
