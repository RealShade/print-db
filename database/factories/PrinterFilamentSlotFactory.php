<?php

namespace Database\Factories;

use App\Models\FilamentSpool;
use App\Models\Printer;
use App\Models\PrinterFilamentSlot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PrinterFilamentSlotFactory extends Factory
{
    protected $model = PrinterFilamentSlot::class;

    public function definition() : array
    {
        return [
            'name'        => $this->faker->unique()->name(),
            'attribute'   => $this->faker->word(),
            'description' => $this->faker->text(),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),

            'printer_id'        => Printer::factory(),
        ];
    }
}
