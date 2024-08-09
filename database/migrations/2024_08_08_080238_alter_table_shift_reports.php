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
        Schema::table('shift_reports', function (Blueprint $table) {
            $table->after('viewed_by', function (Blueprint $table) {
                $table->string('viewed_shift')->nullable();
                $table->dateTime('viewed_at_1')->nullable();
                $table->foreignIdFor(User::class, 'viewed_by_1')->nullable()->constrained('users')->restrictOnDelete();
                $table->string('viewed_shift_1')->nullable();
                $table->dateTime('viewed_at_2')->nullable();
                $table->foreignIdFor(User::class, 'viewed_by_2')->nullable()->constrained('users')->restrictOnDelete();
                $table->string('viewed_shift_2')->nullable();
                $table->boolean('notify')->default(true)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_reports', function (Blueprint $table) {
            $table->dropForeign(['viewed_by_1']);
            $table->dropForeign(['viewed_by_2']);
            $table->dropColumn(['viewed_shift', 'viewed_at_1', 'viewed_by_1', 'viewed_shift_1', 'viewed_at_2', 'viewed_by_2', 'viewed_shift_2', 'notify']);
        });
    }
};
