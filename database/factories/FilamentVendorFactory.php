<?php

namespace Database\Factories;

use App\Models\FilamentVendor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilamentVendorFactory extends Factory
{
    protected $model = FilamentVendor::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'rate' => $this->faker->numberBetween(1, 10),
            'comment' => $this->faker->optional(0.7)->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
