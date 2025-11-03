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
        Schema::table('patients', function (Blueprint $table) {
            // Full-text for names
            $table->fullText(['first_name', 'middle_name', 'last_name']);

            // Phone index
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropFullText(['first_name', 'middle_name', 'last_name']); //drop fulltext
            $table->dropIndex(['phone']); // Laravel knows the column
        });
    }
};
