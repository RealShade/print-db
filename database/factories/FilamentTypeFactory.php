<?php

namespace Database\Factories;

use App\Models\FilamentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilamentTypeFactory extends Factory
{
    protected $model = FilamentType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['PLA', 'PETG', 'ABS', 'TPU', 'Nylon', 'PVA', 'HIPS', 'PC']),
            'user_id' => User::factory(),
        ];
    }
}
