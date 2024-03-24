<?php

use App\Models\Prescription;
use App\Models\ThirdParty;
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
        Schema::create('third_party_services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Prescription::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(ThirdParty::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('third_party_services');
    }
};
