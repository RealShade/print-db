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
        Schema::create('printing_task_logs', function(Blueprint $table) {
            $table->id();
            $table->foreignId('part_task_id')->constrained(app(PartTask::class)->getTable())->cascadeOnDelete();
            $table->foreignId('printer_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('count');
            $table->unsignedTinyInteger('event_source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('printing_task_logs');
    }
};
