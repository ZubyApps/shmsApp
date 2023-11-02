<?php

use App\Models\Resource;
use App\Models\ResourceSupplier;
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
        Schema::create('add_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Resource::class);
            $table->integer('quantity');
            $table->dateTime('expiry_date');
            $table->foreignIdFor(ResourceSupplier::class)->nullable();
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_resources');
    }
};
