<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. First, create the index so the View can use it immediately for speed
        Schema::table('prescriptions', function (Blueprint $table) {
            // We check if it exists first to prevent errors during refactoring
            $table->index(['visit_id', 'resource_id'], 'visit_resource_idx');
        });

        // 2. Create the View
        DB::statement("
            CREATE OR REPLACE VIEW prescription_resource_totals AS
            SELECT 
                visit_id, 
                resource_id, 
                SUM(qty_billed) as total_qty_resource_billed
            FROM prescriptions
            GROUP BY visit_id, resource_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop the View first
        DB::statement("DROP VIEW IF EXISTS prescription_resource_totals");

        

        // 2. Drop the Index
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropIndex('visit_resource_idx');
        });
    }
};
