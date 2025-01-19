<?php

use App\Models\Prescription;
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
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->dateTime('booked_date')->nullable();
            $table->string('comment')->nullable();
            $table->foreignIdFor(User::class, 'date_booked_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('status')->nullable();
            $table->foreignIdFor(User::class, 'status_updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(Prescription::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
