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
            $table->after('resource_id', function (Blueprint $table) {
                $table->integer('hms_stock')->nullable();
                $table->integer('actual_stock')->nullable();
                $table->integer('difference')->nullable();
                
            });
            $table->after('quantity', function (Blueprint $table) {
                $table->integer('final_quantity')->nullable();
                $table->string('comment', 500)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('add_resource_stocks', function (Blueprint $table) {
            $table->dropColumn(['hms_stock', 'actual_stock', 'difference', 'final_quantity', 'comment']);
        });
    }
};
