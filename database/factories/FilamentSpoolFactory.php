<?php

namespace Database\Factories;

use App\Models\Filament;
use App\Models\FilamentPackaging;
use App\Models\FilamentSpool;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilamentSpoolFactory extends Factory
{
    protected $model = FilamentSpool::class;

    public function definition(): array
    {
        $dateFirstUsed = $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now');
        $packaging = FilamentPackaging::factory()->create();

        return [
            'filament_id' => Filament::factory(),
            'filament_packaging_id' => $packaging->id,
            'weight_used' => $this->faker->randomFloat(4, 100, 500),
            'date_first_used' => $dateFirstUsed,
            'date_last_used' => $dateFirstUsed ? $this->faker->dateTimeBetween($dateFirstUsed, 'now') : null,
            'cost' => $this->faker->randomFloat(2, 15, 50),
            'user_id' => User::factory(),
        ];
    }
}
