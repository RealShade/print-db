<?php

namespace Database\Factories;

use App\Models\Filament;
use App\Models\FilamentType;
use App\Models\FilamentVendor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilamentFactory extends Factory
{
    protected $model = Filament::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->colorName() . ' ' . $this->faker->randomElement(['Premium', 'Standard', 'Basic', 'Pro']),
            'filament_vendor_id' => FilamentVendor::factory(),
            'filament_type_id' => FilamentType::factory(),
            'colors' => $this->faker->randomElements(['red', 'blue', 'green', 'yellow', 'black', 'white', 'orange', 'purple'], $this->faker->numberBetween(1, 3)),
            'density' => $this->faker->randomFloat(4, 1.0, 1.5),
            'user_id' => User::factory(),
        ];
    }
}
