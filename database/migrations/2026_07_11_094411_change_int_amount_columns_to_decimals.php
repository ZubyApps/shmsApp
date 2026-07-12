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
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount_paid', 12)->change();
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->decimal('total_hms_bill', 12)->change();
            $table->decimal('total_nhis_bill', 12)->change();
            $table->decimal('total_capitation', 12)->change();
            $table->decimal('total_hmo_bill', 12)->change();
            $table->decimal('total_paid', 12)->change();
            $table->decimal('discount', 12)->change();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->decimal('hms_bill', 12)->change();
            $table->decimal('nhis_bill', 12)->change();
            $table->decimal('hmo_bill', 12)->change();
            $table->decimal('paid', 12)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('amount_paid')->change();
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->integer('total_hms_bill')->change();
            $table->integer('total_nhis_bill')->change();
            $table->integer('total_capitation')->change();
            $table->integer('total_hmo_bill')->change();
            $table->integer('total_paid')->change();
            $table->integer('discount')->change();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->integer('hms_bill')->change();
            $table->integer('nhis_bill')->change();
            $table->integer('hmo_bill')->change();
            $table->integer('paid')->change();
        });
    }
};
