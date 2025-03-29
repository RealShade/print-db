<?php

use App\Enums\TaskStatus2;
use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::table(app(Task::class)->getTable(), function(Blueprint $table) {
            $table->unsignedTinyInteger('status2')->default(0)->after('status');
        });

        foreach (Task::all() as $task) {
            $task->update(['status2' => $task->status->id()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::table(app(Task::class)->getTable(), function(Blueprint $table) {
            $table->dropColumn('status2');
        });
    }
};
