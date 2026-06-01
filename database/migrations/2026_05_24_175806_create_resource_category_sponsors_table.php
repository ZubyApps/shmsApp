<?php

use App\Models\ResourceCategory;
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
        Schema::create('resource_category_sponsor', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Sponsor::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(ResourceCategory::class)->constrained()->restrictOnDelete();
            
            // The % of the price the sponsor is actually charged
            // e.g., 10 for 10%, 100 for full price, 0 for free
            $table->decimal('billable_percentage', 8, 2, true)->default(0.00); 
            
            $table->unique(['sponsor_id', 'resource_category_id'], 'sp_res_cat_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_category_sponsors');
    }
};
