<?php

use App\Models\Patient;
use App\Models\ThirdParty;
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
        Schema::create('patients_files', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('storage_filename');
            $table->string('client_mimetype');
            $table->string('extension');
            $table->text('comment')->nullable();
            $table->foreignIdFor(ThirdParty::class)->nullable()->constrained()->restrictOnDelete();
            $table->foreignIdFor(Patient::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Visit::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients_files');
    }
};
