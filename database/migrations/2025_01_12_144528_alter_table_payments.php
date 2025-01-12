<?php

use App\Models\Patient;
use App\Models\Visit;
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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignIdFor(Patient::class)->nullable()->change();
            $table->foreignIdFor(Visit::class)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignIdFor(Patient::class)->nullable(false)->change();
            $table->foreignIdFor(Visit::class)->nullable(false)->change();
        });
    }
};
