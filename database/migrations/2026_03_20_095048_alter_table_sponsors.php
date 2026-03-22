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
        Schema::table('sponsors', function (Blueprint $table) {
            // Total billable amount (HMS Bill) for all patients under this sponsor
            $table->decimal('total_bill', 15, 2)->default(0)->after('flag');
            
            // Total actually paid by the corporate/family entity
            $table->decimal('total_paid', 15, 2)->default(0)->after('total_bill');
            
            // Total discounts granted to this sponsor's pool
            $table->decimal('total_discount', 15, 2)->default(0)->after('total_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropColumn(['total_bill','total_paid', 'total_discount']);
        });
    }
};
