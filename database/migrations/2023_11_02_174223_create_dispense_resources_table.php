<?php

use App\Models\Consultation;
use App\Models\Resource;
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
        Schema::create('dispense_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Resource::class);
            $table->integer('quantity');
            $table->foreignIdFor(Consultation::class);
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispense_resources');
    }
};
