<?php

use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->date('month_sent_for')->nullable();
            $table->dateTime('set_from')->nullable();
            $table->string('sponsor_category')->nullable();
            $table->integer('max_days');
            $table->string('first_reminder')->nullable();
            $table->dateTime('first_reminder_date')->nullable();
            $table->foreignIdFor(User::class, 'first_reminder_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('second_reminder')->nullable();
            $table->dateTime('second_reminder_date')->nullable();
            $table->foreignIdFor(User::class, 'second_reminder_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('final_reminder')->nullable();
            $table->dateTime('final_reminder_date')->nullable();
            $table->foreignIdFor(User::class, 'final_reminder_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('comment')->nullable();
            $table->boolean('remind')->default(false);
            $table->dateTime('confirmed_paid')->nullable();
            $table->foreignIdFor(User::class, 'confirmed_paid_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(Visit::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Sponsor::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
