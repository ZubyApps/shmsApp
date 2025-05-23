<?php

use App\Models\Patient;
use App\Models\PayMethod;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('amount_paid');
            $table->string('comment')->nullable();
            $table->foreignIdFor(PayMethod::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Patient::class)->constrained()->restrictOnDelete();// this has been made nullable
            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();// this has been made nullable
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
