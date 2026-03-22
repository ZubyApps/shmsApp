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
        Schema::table('patients', function (Blueprint $table) {
            $table->decimal('total_bill', 15, 2)->default(0)->after('is_active');
            $table->decimal('total_paid', 15, 2)->default(0)->after('total_bill');
            $table->decimal('total_discount', 15, 2)->default(0)->after('total_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['total_bill','total_paid', 'total_discount']);
        });
    }
};
