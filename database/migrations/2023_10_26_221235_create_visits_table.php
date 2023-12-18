<?php

use App\Models\Patient;
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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('verification_status')->default(false);
            $table->string('verification_code')->nullable();
            $table->dateTime('consulted')->nullable();
            $table->boolean('closed')->default(false);
            $table->string('total_paid')->nullable();
            $table->foreignIdFor(User::class, 'doctor_done_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'nurse_done_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'pharmacy_done_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'lab_done_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'billing_done_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'hmo_done_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'doctor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(Patient::class)->constrained()->restrictOnDelete();
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
        Schema::dropIfExists('visits');
    }
};
