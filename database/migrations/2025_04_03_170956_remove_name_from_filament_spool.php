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
        Schema::table(app(FilamentSpool::class)->getTable(), function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(app(FilamentSpool::class)->getTable(), function (Blueprint $table) {
            $table->string('name')->after('filament_packaging_id');
        });
    }
};
