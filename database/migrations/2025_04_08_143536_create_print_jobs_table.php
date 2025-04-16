<?php

use App\Models\PrintJob;
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
        Schema::create(app(PrintJob::class)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('status');
            $table->string('filename');
            $table->dateTime('end_time')->nullable();
            $table->timestamps();

            $table->index(['printer_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(app(PrintJob::class)->getTable());
    }
};
