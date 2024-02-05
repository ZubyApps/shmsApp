<?php

use App\Models\Resource;
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
        Schema::create('bulk_requests', function (Blueprint $table) {
            $table->id();
            $table->string('quantity');
            $table->string('department');
            $table->string('note')->nullable();
            $table->string('cost_price');
            $table->string('selling_price');
            $table->dateTime('dispensed')->nullable();
            $table->foreignIdFor(User::class, 'approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(User::class, 'dispensed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(Resource::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_requests');
    }
};
