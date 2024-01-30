<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('username');
            $table->string('phone_number')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('highest_qualification');
            $table->date('date_of_birth');
            $table->string('sex');
            $table->string('created_by')->nullable();
            $table->string('marital_status');
            $table->string('state_of_origin');
            $table->string('next_of_kin')->nullable();
            $table->string('next_of_kin_rship')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->dateTime('date_of_employment');
            $table->dateTime('date_of_exit')->nullable();
            $table->dateTime('login')->nullable();
            $table->dateTime('logout')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
