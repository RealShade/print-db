<?php

namespace Database\Factories;

use App\Enums\PrinterStatus;
use App\Models\Printer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PrinterFactory extends Factory
{
    protected $model = Printer::class;

    public function definition() : array
    {
        return [
            'name'       => $this->faker->word,
            'status'     => PrinterStatus::ACTIVE,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
