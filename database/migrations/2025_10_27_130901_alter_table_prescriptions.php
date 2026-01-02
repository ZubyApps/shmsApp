<?php

use App\Models\User;
use App\Models\WalkIn;
use App\Models\MortuaryService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            // Step 1: Drop the existing foreign key constraint
            $table->dropForeign(['visit_id']);

            // Step 2: Modify the visit_id to be nullable
            $table->foreignId('visit_id')
                  ->nullable()
                  ->change();

            // Step 3: Re-add the foreign key constraint (now allows null)
            $table->foreign('visit_id')
                  ->references('id')
                  ->on('visits')
                  ->onDelete('set null');
        });

        // Step 4: Add the new walk_in_id and mortuary_service_id column
        Schema::table('prescriptions', function (Blueprint $table) {
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

            $table->dateTime('approved_rejected_at')->nullable()->after('rejected_by');
            $table->dateTime('paid_at')->nullable()->after('paid_by');
            $table->dateTime('discountinued_at')->nullable()->after('discontinued');
            $table->dateTime('sample_collected_at')->nullable()->after('test_sample');
            $table->foreignIdFor(User::class, 'sample_collected_by')->nullable()->constrained('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            // Reverse: Drop walk_in_id column and its constraint
            $table->dropForeign(['walk_in_id']);
            $table->dropForeign(['mortuary_service_id']);
            $table->dropForeign(['sample_collected_by']);
            $table->dropColumn(['walk_in_id', 'mortuary_service_id', 'approved_rejected_at', 'paid_at', 'discountinued_at', 'sample_collected_by']);

            // Reverse: Drop the nullable foreign key on visit_id
            $table->dropForeign(['visit_id']);

            // Reverse: Make visit_id NOT NULL again and re-add strict constraint
            $table->foreignId('visit_id')
                  ->change(); // This makes it unsigned big integer, not null by default

            // Re-add the original strict foreign key
            $table->foreign('visit_id')
                  ->references('id')
                  ->on('visits')
                  ->restrictOnDelete();
        });
    }
};
