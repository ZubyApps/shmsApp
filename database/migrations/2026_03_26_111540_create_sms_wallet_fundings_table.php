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
        Schema::create('sms_wallet_fundings', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount_paid', 12, 2);    // Actual Naira paid
            $table->decimal('units_added', 12, 2);    // Units they get for that Naira
            $table->string('payment_method');         // 'paystack', 'bank_transfer', 'admin'
            $table->string('reference')->unique()->nullable();    // Paystack Ref or Teller No
            $table->string('status')->default('pending'); // 'pending', 'paid', 'failed'
            $table->string('approved_by')->nullable();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_wallet_fundings');
    }
};
