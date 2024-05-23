<?php

use App\Models\User;
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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->after('discontinued', function (Blueprint $table) {
                $table->string('held')->nullable();
                $table->dateTime('held_at')->nullable();
                $table->foreignIdFor(User::class, 'held_by')->nullable()->constrained('users')->restrictOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['held', 'held_at', 'held_by']);
        });
    }
};
