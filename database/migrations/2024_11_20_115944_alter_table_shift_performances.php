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
        Schema::table('shift_performances', function (Blueprint $table) {
            $table->after('chart_rate', function (Blueprint $table) {
                $table->string('others_chart_rate')->nullable();
                $table->string('others_done_rate')->nullable();
                $table->string('first_serv_res')->nullable();
                $table->string('service_time')->nullable();
            });
            $table->renameColumn('chart_rate', 'injectables_chart_rate');
            $table->renameColumn('given_rate', 'injectables_given_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_performances', function (Blueprint $table) {
            $table->renameColumn('injectables_chart_rate', 'chart_rate');
            $table->renameColumn('injectables_given_rate', 'given_rate');
            $table->dropColumn(['others_chart_rate', 'others_done_rate', 'first_serv_res', 'service_time']);
        });
    }
};
