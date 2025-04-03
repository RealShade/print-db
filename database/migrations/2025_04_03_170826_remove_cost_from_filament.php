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
        Schema::table(app(Filament::class)->getTable(), function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(app(Filament::class)->getTable(), function (Blueprint $table) {
            $table->decimal('cost')->nullable()->after('density');
        });
    }
};
