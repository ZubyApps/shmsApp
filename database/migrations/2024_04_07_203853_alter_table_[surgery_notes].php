<?php

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
        Schema::table('[surgery_notes]', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('[surgery_notes]', function (Blueprint $table) {
            //
        });
    }
};
