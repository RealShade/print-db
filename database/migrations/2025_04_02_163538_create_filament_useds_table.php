<?php

use App\Models\PartTask;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('filament_used_logs', function(Blueprint $table) {
            $table->id();
            $table->foreignId('filament_spool_id')->constrained()->cascadeOnDelete();
            $table->foreignId('printer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('part_task_id')->nullable()->constrained(app(PartTask::class)->getTable())->nullOnDelete();
            $table->decimal('weight_used', 10, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('filament_used_logs');
    }
};
