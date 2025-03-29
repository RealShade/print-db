<?php

use App\Models\Task;
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
        Schema::table(app(Task::class)->getTable(), function (Blueprint $table) {
            $table->dropColumn('status');
            $table->renameColumn('status2', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(app(Task::class)->getTable(), function (Blueprint $table) {
            $table->renameColumn('status', 'status2');

            // Воссоздаем старый столбец status с типом json
            // Убедитесь, что этот тип соответствует типу исходного столбца
            $table->string('status')->after('count_set_planned')->nullable();
        });
    }
};
