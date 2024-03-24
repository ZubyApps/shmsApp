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
            $table->date('date');
            $table->dateTime('time_of_admission');
            $table->dateTime('time_of_delivery');
            $table->string('apgar_score');
            $table->string('birth_weight');
            $table->string('mode_of_delivery');
            $table->string('parity');
            $table->string('head_circumference');
            $table->integer('female')->nullable();
            $table->integer('male')->nullable();
            $table->string('ebl');
            $table->text('note');
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
