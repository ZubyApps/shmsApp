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
        Schema::table('add_resource_stocks', function (Blueprint $table) {
            $table->after('quantity', function (Blueprint $table) {
                $table->string('hms_stock')->nullable();
                $table->string('actual_stock')->nullable();
                $table->string('difference')->nullable();
                $table->string('quantity_added')->nullable();

            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('add_resource_stocks', function (Blueprint $table) {
            $table->dropColumn(['hms_stock', 'actual_stock', 'difference', 'quantity_added']);
        });
    }
};
