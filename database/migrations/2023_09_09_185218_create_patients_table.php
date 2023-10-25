<?php

use App\Enum\PatientType;
use App\Models\Sponsor;
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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('card_no')->unique();
            $table->string('patient_type');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('sex');
            $table->string('marital_status')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('state_of_residence');
            $table->string('email')->unique()->nullable();
            $table->string('staff_id')->nullable();
            $table->string('nationality')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('occupation')->nullable();
            $table->string('religion')->nullable();
            $table->string('ethnic_group')->nullable();
            $table->string('next_of_kin')->nullable();
            $table->string('next_of_kin_rship')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->string('known_conditions')->nullable();
            $table->string('registration_bill')->nullable();
            $table->boolean('is_active')->default(false);
            $table->foreignIdFor(Sponsor::class);
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
