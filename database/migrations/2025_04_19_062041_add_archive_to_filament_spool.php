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
            $table->boolean('archived')->default(false)->after('cost');
            $table->dateTime('archived_at')->nullable()->after('archived');

            $table->index(['date_last_used', 'archived']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filament_spools', function (Blueprint $table) {
            $table->dropColumn('archived');
            $table->dropColumn('archived_at');
            $table->dropIndex(['date_last_used', 'archived']);
        });
    }
};
