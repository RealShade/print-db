<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->string('stl_filename')->nullable();
            $table->string('stl_original_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('stl_filename');
            $table->dropColumn('stl_original_name');
        });
    }
};
