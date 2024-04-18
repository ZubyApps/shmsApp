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
        Schema::table('visits', function (Blueprint $table) {
            $table->after('hmo_done_by', function (Blueprint $table) {
                $table->dateTime('doctor_done_at')->nullable();
                $table->dateTime('nurse_done_at')->nullable();
                $table->foreignIdFor(User::class, 'lab_done_by')->nullable()->constrained('users')->restrictOnDelete();
                $table->foreignIdFor(User::class, 'waiting_for')->nullable()->constrained('users')->restrictOnDelete();
                $table->boolean('reviewed')->default(false)->nullable();
                $table->boolean('resolved')->default(false)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['lab_done_by', 'waiting_for', 'doctor_done_at', 'nurse_done_at', 'reviewed', 'resolved']);
        });
    }
};
