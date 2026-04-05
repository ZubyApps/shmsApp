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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type_id')->index();
            $table->text('message');
            $table->string('message_type');
            $table->string('recipient_name');
            $table->string('recipient_contact');
            $table->string('network'); //mtn
            $table->decimal('units_deducted', 12, 2);
            $table->string('sender')->nullable();
            $table->string('status')->default('pending');
            $table->string('reference_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
