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
        Schema::create('surgery_notes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type_of_operation');
            $table->string('type_of_aneasthesia');
            $table->string('surgeon');
            $table->string('assistant_surgeon')->nullable();
            $table->string('aneasthetist')->nullable();
            $table->string('scrub_nurse')->nullable();
            $table->string('surgical_procedure');
            $table->string('surgeons_notes');
            $table->string('aneasthetists_notes')->nullable();
            $table->string('post_op_notes');
            $table->string('pre_assessment')->nullable();
            $table->string('indication')->nullable();
            $table->string('surgery')->nullable();
            $table->string('plan')->nullable();
            $table->string('pre_med')->nullable();
            $table->string('baseline')->nullable();
            $table->string('cannulation')->nullable();
            $table->string('pre_loading')->nullable();
            $table->string('induction')->nullable();
            $table->string('maintainance')->nullable();
            $table->string('infusion')->nullable();
            $table->string('analgesics')->nullable();
            $table->string('transfusion')->nullable();
            $table->string('antibiotics')->nullable();
            $table->string('kos')->nullable();
            $table->string('eos')->nullable();
            $table->string('ebl')->nullable();
            $table->string('immediate_post_op')->nullable();
            $table->dateTime('tourniquet_time')->nullable();
            $table->dateTime('tourniquet_out')->nullable();
            $table->dateTime('baby_out')->nullable();
            $table->integer('female')->nullable();
            $table->integer('male')->nullable();
            $table->string('apgar_score')->nullable();
            $table->string('weight')->nullable();
            $table->string('cs_surgeon')->nullable();
            $table->string('cs_anaesthetist')->nullable();
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
        Schema::dropIfExists('surgery_notes');
    }
};
