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
        Schema::create('unit_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12, 2); 
            $table->decimal('running_balance', 12, 2);
            $table->string('type'); 
            $table->string('reference');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_transactions');
    }
};
