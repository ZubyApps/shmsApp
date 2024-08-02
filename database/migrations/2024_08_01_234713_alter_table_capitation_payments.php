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
        Schema::table('capitation_payments', function (Blueprint $table) {
            $table->decimal('amount_paid', 12)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capitation_payments', function (Blueprint $table) {
            $table->float('amount_paid')->change();
        });
    }
};
