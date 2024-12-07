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
            $table->after('comment', function (Blueprint $table) {
                $table->integer('final_stock')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('add_resource_stocks', function (Blueprint $table) {
            $table->dropColumn(['final_stock']);
        });
    }
};
