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
            $table->text('prescription')->nullable();
            $table->text('note')->nullable();
            $table->integer('qty_billed')->default(0);
            $table->integer('hms_bill')->default(0);
            $table->integer('nhis_bill')->default(0);
            $table->float('capitation')->default(0);
            $table->dateTime('hms_bill_date')->nullable();
            $table->foreignIdFor(User::class, 'hms_bill_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->integer('hmo_bill')->default(0);
            $table->dateTime('hmo_bill_date')->nullable();
            $table->string('hmo_bill_note')->nullable();
            $table->foreignIdFor(User::class, 'hmo_bill_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->integer('qty_dispensed')->default(0);
            $table->dateTime('dispense_date')->nullable();
            $table->string('dispense_comment')->nullable();
            $table->foreignIdFor(User::class, 'dispensed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->boolean('approved')->default(false);
            $table->foreignIdFor(User::class, 'approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->boolean('rejected')->default(false);
            $table->foreignIdFor(User::class, 'rejected_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('hmo_note')->nullable();
            $table->string('test_sample')->nullable();
            $table->text('result')->nullable();
            $table->dateTime('result_date')->nullable();
            $table->foreignIdFor(User::class, 'result_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->integer('paid')->default(0);
            $table->foreignIdFor(User::class, 'paid_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->boolean('chartable')->default(false);
            $table->boolean('discontinued')->default(false);
            $table->foreignIdFor(User::class, 'discontinued_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'doctor_on_call')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(Resource::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Consultation::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
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
