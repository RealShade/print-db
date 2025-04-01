<?php

use App\Models\Filament;
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
        Schema::create(app(Filament::class)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('filament_vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('filament_type_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('colors')->nullable();
            $table->decimal('density', 4)->nullable();
            $table->decimal('cost')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(app(Filament::class)->getTable());
    }
};
