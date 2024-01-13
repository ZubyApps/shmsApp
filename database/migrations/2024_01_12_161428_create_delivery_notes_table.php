<?php

use App\Models\Consultation;
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
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->dateTime('date_of_admission');
            $table->dateTime('date_of_delivery');
            $table->dateTime('apgar_score');
            $table->dateTime('birth_weight');
            $table->dateTime('mode_of_delivery');
            $table->dateTime('length_of_parity');
            $table->dateTime('head_circumference');
            $table->dateTime('sex');
            $table->dateTime('ebl');
            $table->foreignIdFor(Consultation::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};
