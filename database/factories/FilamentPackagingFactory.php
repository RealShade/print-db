<?php

namespace Database\Factories;

use App\Models\FilamentPackaging;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilamentPackagingFactory extends Factory
{
    protected $model = FilamentPackaging::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Small Spool', 'Standard Spool', 'Large Spool', 'Mini Spool', 'XL Spool']),
            'weight' => $this->faker->randomElement([250, 500, 750, 1000, 2000, 3000, 5000]),
            'user_id' => User::factory(),
        ];
    }
}
