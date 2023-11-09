<?php

use App\Models\ResourceSubCategory;
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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('flag')->nullable();
            $table->integer('reorder_level')->default(0);
            $table->integer('purchase_price')->default(0);
            $table->integer('selling_price')->default(0);
            $table->string('unit_description')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('stock_level')->default(0)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignIdFor(ResourceSubCategory::class);
            $table->foreignIdFor(ResourceSupplier::class)->nullable();
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
