<?php

use App\Models\Resource;
use App\Models\ResourceSupplier;
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
        Schema::create('add_resource_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Resource::class);
            $table->integer('quantity');
            $table->string('unit_purchase');
            $table->integer('purchase_price');
            $table->integer('selling_price');
            $table->dateTime('expiry_date')->nullable();
            $table->foreignIdFor(ResourceSupplier::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_resource_stocks');
    }
};
