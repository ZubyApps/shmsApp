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
        // Adding indexes to the core financial/search tables
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->index('created_at'); 
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('medication_charts', function (Blueprint $table) {
            $table->index('scheduled_time');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('vital_signs', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', fn($t) => $t->dropIndex(['created_at']));
        Schema::table('visits', fn($t) => $t->dropIndex(['created_at']));
        Schema::table('payments', fn($t) => $t->dropIndex(['created_at']));
        Schema::table('expenses', fn($t) => $t->dropIndex(['created_at']));
        Schema::table('medication_charts', fn($t) => $t->dropIndex(['scheduled_time']));
        Schema::table('patients', fn($t) => $t->dropIndex(['created_at']));
        Schema::table('vital_signs', fn($t) => $t->dropIndex(['created_at']));
    }
};
