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
        Schema::rename('filament_slots', 'printer_filament_slots');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('printer_filament_slots', 'filament_slots');
    }
};
