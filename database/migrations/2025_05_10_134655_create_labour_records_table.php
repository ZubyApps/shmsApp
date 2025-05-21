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
        Schema::create('labour_records', function (Blueprint $table) {
            $table->id();
            
            // Demographic and pregnancy details
            $table->string('parity')->nullable();
            $table->integer('no_of_living_children')->nullable();
            $table->date('lmp')->nullable();
            $table->date('edd')->nullable();
            $table->string('ega')->nullable();
            $table->dateTime('onset')->nullable();
            $table->float('onset_hours')->nullable();
            $table->boolean('spontaneous')->default(false);
            $table->boolean('induced')->default(false);
            $table->boolean('amniotomy')->default(false);
            $table->boolean('oxytocies')->default(false);
            $table->string('cervical_dilation')->nullable(false);
            $table->dateTime('m_ruptured_at')->nullable();
            $table->dateTime('contractions_began')->nullable();
            
            // Contraction quality
            $table->boolean('excellent')->default(false);
            $table->boolean('good')->default(false);
            $table->boolean('fair')->default(false);
            $table->boolean('poor')->default(false);
            
            // Physical measurements
            $table->string('fundal_height')->nullable();
            $table->boolean('multiple')->default(false);
            $table->boolean('singleton')->default(false);
            $table->string('lie')->nullable();
            $table->string('presentation')->nullable();
            $table->string('position')->nullable();
            $table->string('descent')->nullable();
            $table->string('foetal_heart_rate')->nullable();
            $table->string('vulva')->nullable();
            $table->string('vagina')->nullable();
            $table->string('cervix')->nullable();
            $table->boolean('applied_to_pp')->default(false);
            $table->string('os')->nullable();
            $table->boolean('membranes_ruptured')->default(false);
            $table->boolean('membranes_intact')->default(false);
            $table->string('pp_at_o')->nullable();
            $table->string('station_in')->nullable();
            $table->string('caput')->nullable();
            $table->string('moulding')->nullable();
            $table->string('sp')->nullable();
            $table->string('sacral_curve')->nullable();
            $table->string('forecast')->nullable();
            $table->string('ischial_spine')->nullable();
            $table->string('examiner')->nullable();
            $table->string('designation')->nullable();
            $table->text('past_ob_history')->nullable();
            $table->text('antenatal_history')->nullable();
            
            // Labor interventions
            $table->boolean('sol_amniotomy')->default(false);
            $table->string('sol_a_indication')->nullable();
            $table->boolean('sol_oxytocin')->default(false);
            $table->string('sol_o_indication')->nullable();
            $table->boolean('sol_prostaglandins')->default(false);
            $table->string('sol_p_indication')->nullable();
            $table->float('d_of_labour')->nullable();
            $table->boolean('sol_spontaneous')->default(false);
            $table->boolean('sol_assisted')->default(false);
            $table->boolean('sol_forceps')->default(false);
            $table->boolean('extraction')->default(false);
            $table->boolean('vacuum')->default(false);
            $table->boolean('internal_pod_version')->default(false);
            $table->boolean('caesarean_section')->default(false);
            $table->boolean('destructive_operation')->default(false);
            $table->string('d_o_specify')->nullable();
            $table->boolean('anaesthesia')->default(false);
            
            // Third stage of labor
            $table->boolean('p_spontaneous')->default(false);
            $table->boolean('cct')->default(false);
            $table->boolean('manual_removal')->default(false);
            $table->boolean('complete')->default(false);
            $table->boolean('incomplete')->default(false);
            $table->string('placenta_weight')->nullable();
            $table->boolean('perineum_intact')->default(false);
            $table->boolean('first_degree_laceration')->default(false);
            $table->boolean('second_degree_laceration')->default(false);
            $table->boolean('third_degree_laceration')->default(false);
            $table->boolean('episiotomy')->default(false);
            $table->string('repair_by')->nullable();
            $table->string('designation_repair')->nullable(); // Renamed to avoid duplicate
            $table->integer('no_of_skin_sutures')->nullable();
            $table->string('blood_loss')->nullable();
            
            // Neonatal details
            $table->boolean('alive')->default(false);
            $table->string('sexes')->nullable();
            $table->string('baby_weight')->nullable();
            $table->string('apgar_score_1m')->nullable();
            $table->string('apgar_score_5m')->nullable();
            $table->boolean('fresh_still_birth')->default(false);
            $table->boolean('macerated_still_birth')->default(false);
            $table->boolean('immediate_neonatal_death')->default(false);
            $table->boolean('malformation')->default(false);
            
            // Maternal condition
            $table->string('mc_uterus')->nullable();
            $table->string('mc_bladder')->nullable();
            $table->string('mc_blood_pressure')->nullable();
            $table->string('mc_pulse_rate')->nullable();
            $table->string('mc_temperature')->nullable();
            $table->string('mc_respiration')->nullable();
            $table->string('supervisor')->nullable();
            $table->text('blood_loss_treatment')->nullable();
            $table->text('malformation_details')->nullable();
            $table->text('accoucheur')->nullable();

            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class, 'summarized_by')->nullable()->constrained('users')->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labour_records');
    }
};
