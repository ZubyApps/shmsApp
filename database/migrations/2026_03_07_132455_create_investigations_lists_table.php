<?php

use App\Models\User;
use App\Models\Visit;
use App\Models\WalkIn;
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
        // Schema::create('investigations_lists', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignIdFor(Visit::class)->nullable()->constrained()->restrictOnDelete();
        //     $table->foreignIdFor(WalkIn::class)->nullable()->constrained()->restrictOnDelete();
        //     $table->unsignedTinyInteger('status')->default(0);
        //     $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
        //     $table->timestamps();
        // });

        Schema::create('investigations_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Visit::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(WalkIn::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->unsignedInteger('queue_number');
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamp('started_processing_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('queue_date')->virtualAs('DATE(created_at)')->index();
            $table->timestamps();
            $table->index(['queue_date', 'queue_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investigations_lists');
    }
};
