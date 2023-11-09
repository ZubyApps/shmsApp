<?php

use App\Models\Consultation;
use App\Models\Resource;
use App\Models\User;
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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Resource::class);
            $table->text('prescription')->nullable();
            $table->integer('quantity')->default(0)->nullable();
            $table->integer('bill')->default(0)->nullable();
            $table->string('test_result')->nullable();
            $table->foreignIdFor(Consultation::class);
            $table->foreignIdFor(Visit::class);
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
