<?php

use App\Models\Consultation;
use App\Models\Payment;
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
            $table->string('qty_billed')->nullable();
            $table->string('hms_bill')->nullable();
            $table->dateTime('hms_bill_date')->nullable();
            $table->foreignIdFor(User::class, 'hms_bill_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('hmo_bill')->nullable();
            $table->dateTime('hmo_bill_date')->nullable();
            $table->string('hmo_bill_note')->nullable();
            $table->foreignIdFor(User::class, 'hmo_bill_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('qty_dispensed')->nullable();
            $table->dateTime('dispense_date')->nullable();
            $table->string('dispense_comment')->nullable();
            $table->foreignIdFor(User::class, 'dispensed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->boolean('approved')->default(false);
            $table->foreignIdFor(User::class, 'approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->boolean('rejected')->default(false);
            $table->foreignIdFor(User::class, 'rejected_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('hmo_note')->nullable();
            $table->string('test_sample')->nullable();
            $table->string('result')->nullable();
            $table->dateTime('result_date')->nullable();
            $table->foreignIdFor(User::class, 'result_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('paid')->nullable();
            $table->boolean('chartable')->default(false);
            $table->boolean('discontinued')->default(false);
            $table->foreignIdFor(User::class, 'discontinued_by')->nullable()->constrained('users')->restrictOnDelete();
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
