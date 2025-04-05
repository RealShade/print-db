<?php

use App\Models\FilamentLoaded;
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
        Schema::create(app(FilamentLoaded::class)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('filament_spool_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('attribute');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['name', 'printer_id']);
            $table->unique(['attribute', 'printer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(app(FilamentLoaded::class)->getTable());
    }
};
