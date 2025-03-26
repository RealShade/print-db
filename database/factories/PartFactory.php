<?php

namespace Database\Factories;

use App\Models\Part;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PartFactory extends Factory
{
    protected $model = Part::class;

/* **************************************** Public **************************************** */
    public function definition() : array
    {
        return [
            'name'         => $this->faker->sentence(),
            'version'      => $this->faker->randomNumber(),
            'version_date' => Carbon::now(),
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
