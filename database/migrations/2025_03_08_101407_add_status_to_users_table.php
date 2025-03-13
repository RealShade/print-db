<?php

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
        Schema::table(app(User::class)->getTable(), function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(app(User::class)->getTable(), function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
