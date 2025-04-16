<?php

namespace Database\Factories;

use App\Enums\PrintJobStatus;
use App\Models\Printer;
use App\Models\PrintJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PrintJobFactory extends Factory
{
    protected $model = PrintJob::class;

    public function definition() : array
    {
        return [
            'status'     => PrintJobStatus::PRINTING,
            'filename'   => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'printer_id' => Printer::factory(),
        ];
    }
}
