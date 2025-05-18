<?php

use App\Models\LabourRecord;
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
        Schema::create('partographs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('recorded_at');
            $table->enum('parameter_type', [
                'cervical_dilation',
                'descent',
                'uterine_contractions',
                'blood_pressure',
                'pulse',
                'temperature',
                'urine',
                'caput',
                'position',
                'moulding',
                'oxytocin',
                'fluid',
                'drug',
                'fetal_heart_rate'
            ]);
            $table->json('value');
            $table->foreignIdFor(LabourRecord::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class, 'updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->index(['parameter_type', 'recorded_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partographs');
    }
};
