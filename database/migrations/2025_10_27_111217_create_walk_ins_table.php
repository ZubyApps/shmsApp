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
        Schema::create('walk_ins', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->string('sex');
            $table->string('address')->nullable();
            $table->string('occupation')->nullable();
            $table->boolean('prev_xray')->nullable()->default(false);
            $table->date('date_of_xray')->nullable();
            $table->string('clinical_diagnosis')->nullable();
            $table->string('clinical_features')->nullable();
            $table->integer('total_bill')->default(0);
            $table->integer('total_paid')->default(0);
            $table->dateTime('linked_at')->nullable();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walk_ins');
    }
};
