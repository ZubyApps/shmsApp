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
            $table->text('prescription');
            $table->text('instruction')->nullable();
            $table->string('qty_billed')->nullable();
            $table->string('bill')->nullable();
            $table->dateTime('bill_date')->nullable();
            $table->string('qty_dispensed')->nullable();
            $table->dateTime('dispense_date')->nullable();
            $table->string('result')->nullable();
            $table->boolean('approved')->default(false);
            $table->string('paid')->nullable();
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
