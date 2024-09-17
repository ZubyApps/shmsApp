<?php

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
        Schema::create('patient_pre_forms', function (Blueprint $table) {
            $table->id();
            $table->string('short_link')->nullable();
            $table->string('card_no');
            $table->string('patient_type');
            $table->string('sponsor_category');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('sex')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('state_of_residence')->nullable();
            $table->string('email')->nullable();
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
            $table->text('known_conditions')->nullable();
            $table->string('registration_bill')->nullable();
            $table->foreignIdFor(Sponsor::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_pre_forms');
    }
};
