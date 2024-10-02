<?php

use App\Models\Resource;
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
        Schema::table('bulk_requests', function (Blueprint $table) {
            $table->foreignIdFor(Resource::class, 'deducted_from')->nullable()->constrained('resources')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_requests', function (Blueprint $table) {
            $table->dropForeign(['deducted_from']);
            $table->dropColumn(['deducted_from']);
        });
    }
};
