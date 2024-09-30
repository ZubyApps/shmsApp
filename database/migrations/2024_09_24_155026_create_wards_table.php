<?php

use App\Models\User;
use App\Models\Visit;
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
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('short_name');
            $table->string('long_name');
            $table->string('bed_number');
            $table->string('description');
            $table->string('bill')->nullable();
            $table->boolean('flag')->default(false)->nullable();
            $table->string('flag_reason')->nullable();
            $table->foreignIdFor(Visit::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
