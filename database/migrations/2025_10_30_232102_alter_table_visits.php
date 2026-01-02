<?php

use App\Models\Ward;
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
        Schema::table('visits', function (Blueprint $table) {
            $table->after('nurse_done_at', function (Blueprint $table) {
                $table->dateTime('hmo_done_at')->nullable();
            });
            $table->after('ward', function (Blueprint $table) {
                $table->foreignIdFor(Ward::class)->nullable()->constrained()->restrictOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign(['ward_id']);
            $table->dropColumn(['ward_id', 'hmo_done_at']);
        });
    }
};
