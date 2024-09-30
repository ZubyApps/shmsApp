<?php

use App\Models\UnitDescription;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('add_resource_stocks', function (Blueprint $table) {
            $table->foreignIdFor(UnitDescription::class)->nullable()->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('add_resource_stocks', function (Blueprint $table) {
            $table->dropForeign(['unit_description_id']);
            $table->dropColumn(['unit_description_id']);
        });
    }
};
