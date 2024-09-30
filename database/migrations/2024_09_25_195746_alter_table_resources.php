<?php

use App\Models\MarkedFor;
use App\Models\UnitDescription;
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
        Schema::table('resources', function (Blueprint $table) {
            $table->foreignIdFor(MarkedFor::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(UnitDescription::class)->nullable()->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['marked_for_id']);
            $table->dropForeign(['unit_description_id']);
            $table->dropColumn(['marked_for_id', 'unit_description_id']);
        });
    }
};
