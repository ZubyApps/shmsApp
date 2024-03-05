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
            $table->string('verification_code')->nullable();
            $table->string('verification_status')->nullable();
            $table->string('discharge_reason')->nullable();
            $table->text('discharge_remark')->nullable();
            $table->string('admission_status')->nullable();
            $table->string('ward')->nullable();
            $table->string('bed_no')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->foreignIdFor(User::class, 'verified_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('consulted')->nullable();
            $table->dateTime('viewed_at')->nullable();
            $table->foreignIdFor(User::class, 'viewed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('closed_opened_at')->nullable();
            $table->boolean('closed')->default(false);
            $table->foreignIdFor(User::class, 'closed_opened_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->integer('discount')->nullable();
            $table->foreignIdFor(User::class, 'discount_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->integer('total_hms_bill')->default(0);
            $table->integer('total_hmo_bill')->default(0);
            $table->integer('total_paid')->default(0);
            $table->foreignIdFor(User::class, 'sponsor_changed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'doctor_done_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'nurse_done_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'pharmacy_done_by')->nullable()->constrained('users')->restrictOnDelete();
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
