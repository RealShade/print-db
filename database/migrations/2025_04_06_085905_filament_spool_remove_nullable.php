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
        Schema::table('filament_spools', function (Blueprint $table) {
            DB::table('filament_spools')->whereNull('weight_initial')->update(['weight_initial' => 0]);
            DB::table('filament_spools')->whereNull('weight_used')->update(['weight_used' => 0]);
            $table->decimal('weight_initial', 10, 4)->default(0)->change();
            $table->decimal('weight_used', 10, 4)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filament_spools', function (Blueprint $table) {
            $table->decimal('weight_initial', 10, 4)->nullable()->change();
            $table->decimal('weight_used', 10, 4)->nullable()->change();
            DB::table('filament_spools')->where('weight_initial', 0)->update(['weight_initial' => null]);
            DB::table('filament_spools')->where('weight_used', 0)->update(['weight_used' => null]);
        });
    }
};
