<?php

use App\Models\MortuaryService;
use App\Models\WalkIn;
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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignIdFor(WalkIn::class)
                  ->nullable()
                  ->after('visit_id')
                  ->constrained()
                  ->restrictOnDelete();

            $table->foreignIdFor(MortuaryService::class)
                  ->nullable()
                  ->after('walk_in_id')
                  ->constrained()
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Reverse: Drop walk_in_id and mortuary_service_id columns and their constraints
            $table->dropForeign(['walk_in_id']);
            $table->dropForeign(['mortuary_service_id']);
            $table->dropColumn(['walk_in_id', 'mortuary_service_id']);
        });
    }
};
