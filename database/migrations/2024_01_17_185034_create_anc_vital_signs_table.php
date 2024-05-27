<?php

use App\Models\AntenatalRegisteration;
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
        Schema::create('anc_vital_signs', function (Blueprint $table) {
            $table->id();
            $table->string('ho_fundus')->nullable();
            $table->string('p_position')->nullable();
            $table->string('roppt_brim')->nullable();
            $table->string('fh_rate')->nullable();
            $table->string('urine_protein')->nullable();
            $table->string('urine_glucose')->nullable();
            $table->string('blood_pressure');
            $table->string('weight')->nullable();
            $table->string('hb')->nullable();
            $table->string('oedema')->nullable();
            $table->text('remarks')->nullable();
            $table->string('return')->nullable();
            $table->foreignIdFor(AntenatalRegisteration::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anc_vital_signs');
    }
};
