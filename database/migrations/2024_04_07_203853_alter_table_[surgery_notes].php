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
        Schema::table('surgery_notes', function (Blueprint $table) {
            $table->text('surgical_procedure')->change();
            $table->text('surgeons_notes')->change();
            $table->text('aneasthetists_notes')->nullable()->change();
            $table->text('post_op_notes')->change();
            $table->text('pre_assessment')->nullable()->change();
            $table->text('indication')->nullable()->change();
            $table->dateTime('kos')->nullable()->change();
            $table->dateTime('eos')->nullable()->change();
            $table->text('immediate_post_op')->nullable()->change();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surgery_notes', function (Blueprint $table) {
            //
        });
    }
};
