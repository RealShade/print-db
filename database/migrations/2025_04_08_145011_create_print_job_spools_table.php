<?php

use App\Models\PrintJobSpool;
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
        Schema::create(app(PrintJobSpool::class)->getTable(), function (Blueprint $table) {
            $table->foreignId('print_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('filament_spool_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight_used', 10, 4);

            $table->primary(['print_job_id', 'filament_spool_id']);
            $table->index('filament_spool_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(app(PrintJobSpool::class)->getTable());
    }
};
