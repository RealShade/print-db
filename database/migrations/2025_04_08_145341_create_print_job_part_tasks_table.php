<?php

use App\Models\PartTask;
use App\Models\PrintJobPartTask;
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
        Schema::create(app(PrintJobPartTask::class)->getTable(), function (Blueprint $table) {
            $table->foreignId('print_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('part_task_id')->constrained(app(PartTask::class)->getTable())->cascadeOnDelete();
            $table->unsignedInteger('count_printed');

            $table->primary(['print_job_id', 'part_task_id']);
            $table->index('part_task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(app(PrintJobPartTask::class)->getTable());
    }
};
