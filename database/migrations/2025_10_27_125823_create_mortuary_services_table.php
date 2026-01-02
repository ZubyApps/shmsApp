<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mortuary_services', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_deposited')->nullable();
            $table->string('deceased_name');
            $table->string('deceased_sex')->nullable();
            $table->string('depositors_name');
            $table->string('depositors_address')->nullable();
            $table->string('depositors_phone');
            $table->string('depositors_relationship')->nullable();
            $table->string('alt_collectors_name')->nullable();
            $table->string('alt_collectors_address')->nullable();
            $table->string('alt_collectors_phone')->nullable();
            $table->string('alt_collectors_relationship')->nullable();
            $table->date('pickup_date')->nullable();
            $table->dateTime('date_collected')->nullable();
            $table->foreignIdFor(User::class, 'date_collected_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->integer('no_of_days')->default(0);
            $table->integer('total_bill')->default(0);
            $table->integer('total_paid')->default(0);
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mortuary_services');
    }
};
