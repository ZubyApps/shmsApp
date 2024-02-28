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
        Schema::create('antenatal_registerations', function (Blueprint $table) {
            $table->id();
            $table->string('marital_status');
            $table->string('husbands_name')->nullable();
            $table->string('husbands_occupation')->nullable();
            $table->string('heart_disease')->nullable();
            $table->string('chest_disease')->nullable();
            $table->string('kidney_disease')->nullable();
            $table->string('blood_transfusion')->nullable();
            $table->string('diabetes')->nullable();
            $table->string('hypertension')->nullable();
            $table->string('sickle_cell')->nullable();
            $table->string('others')->nullable();
            $table->string('multiple_pregnancy')->nullable();
            $table->dateTime('lmp');
            $table->dateTime('edd');
            $table->string('ega');
            $table->string('previous_pregnancies')->nullable();
            $table->string('total_pregnancies')->nullable();
            $table->string('no_of_living_children')->nullable();
            $table->string('bleeding')->nullable();
            $table->string('discharge')->nullable();
            $table->string('urinary_symptoms')->nullable();
            $table->string('swelling_of_ankles')->nullable();
            $table->string('other_symptoms')->nullable();
            $table->string('general_condition')->nullable();
            $table->string('oedema')->nullable();
            $table->string('general_condition_anemia')->nullable();
            $table->string('anemia')->nullable();
            $table->string('abdomen')->nullable();
            $table->string('specimen')->nullable();
            $table->string('specimen_cm')->nullable();
            $table->string('liver')->nullable();
            $table->string('liver_cm')->nullable();
            $table->string('virginal_examination')->nullable();
            $table->string('other_anomalies')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('bp')->nullable();
            $table->string('urine')->nullable();
            $table->string('breast_nipples')->nullable();
            $table->string('hb')->nullable();
            $table->string('genotype')->nullable();
            $table->string('vdrl')->nullable();
            $table->string('group_hr')->nullable();
            $table->string('rvst')->nullable();
            $table->string('comments')->nullable();
            $table->string('instr_rel_to_peuperium')->nullable();
            $table->string('assessment')->nullable();
            $table->string('hb_genotype')->nullable();
            $table->string('chest_xray')->nullable();
            $table->string('rhesus')->nullable();
            $table->string('ant_mal_and_specific_therapies')->nullable();
            $table->string('pelvic_assessment')->nullable();
            $table->string('instr_for_delivery')->nullable();
            $table->foreignIdFor(Patient::class)->constrained()->restrictOnDelete();
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
        Schema::dropIfExists('antenatal_registerations');
    }
};
