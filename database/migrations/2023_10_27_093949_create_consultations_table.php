<?php

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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('specialist')->nullable();
            $table->text('p_complain')->nullable();
            $table->text('hop_complain')->nullable();
            $table->text('med_surg_history')->nullable();
            $table->text('obgyn_history')->nullable();
            $table->text('paediatric_history')->nullable();
            $table->text('exam_findings')->nullable();
            $table->text('icd11_diagnosis')->nullable();
            $table->text('provisional_diagnosis')->nullable();
            $table->text('phys_plan')->nullable();
            $table->text('complaint')->nullable();
            $table->text('assessment')->nullable();
            $table->text('history_of_care')->nullable();
            $table->text('notes')->nullable();
            $table->text('roppt_brim')->nullable();
            $table->text('remarks')->nullable();
            $table->string('p_position')->nullable();
            $table->string('ho_fundus')->nullable();
            $table->dateTime('lmp')->nullable();
            $table->dateTime('edd')->nullable();
            $table->string('ega')->nullable();
            $table->string('fh_rate')->nullable();
            $table->string('admission_status');
            $table->string('ward')->nullable();
            $table->string('bed_no')->nullable();
            $table->boolean('specialist_consultation')->default(false)->nullable();
            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class, 'updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
