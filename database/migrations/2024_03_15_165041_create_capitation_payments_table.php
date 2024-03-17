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
        Schema::create('capitation_payments', function (Blueprint $table) {
            $table->id();
            $table->date('month_paid_for');
            $table->integer('number_of_lives');
            $table->float('amount_paid');
            $table->string('bank')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('capitation_payments');
    }
};
