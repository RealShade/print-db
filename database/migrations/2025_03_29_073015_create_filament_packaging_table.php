<?php

use App\Models\FilamentPackaging;
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
        Schema::create(app(FilamentPackaging::class)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('weight')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(app(FilamentPackaging::class)->getTable());
    }
};
