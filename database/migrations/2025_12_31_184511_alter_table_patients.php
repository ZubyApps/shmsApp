<?php

use App\Models\User;
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
        Schema::table('patients', function (Blueprint $table) {
            $table->after('flag_reason', function (Blueprint $table) {
                $table->foreignIdFor(User::class, 'flagged_by')->nullable()->constrained('users')->restrictOnDelete();
                $table->dateTime('flagged_at')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['flagged_by']);
            $table->dropColumn(['flagged_by', 'flagged_at']);
        });
    }
};
