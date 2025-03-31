<?php

use App\Models\FilamentSpool;
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
        Schema::create(app(FilamentSpool::class)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('filament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('filament_packaging_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->decimal('weight_initial', 10, 4)->nullable();
            $table->decimal('weight_used', 10, 4)->nullable();
            $table->dateTime('date_first_used')->nullable();
            $table->dateTime('date_last_used')->nullable();
            $table->decimal('cost')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(app(FilamentSpool::class)->getTable());
    }
};
