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
            'name' => $this->faker->word() . ' Spool',
            'filament_id' => Filament::factory(),
            'filament_packaging_id' => $packaging->id,
            'weight_initial' => $this->faker->randomFloat(4, 2000, 3000),
            'weight_used' => $this->faker->randomFloat(4, 1, 2000),
            'date_first_used' => $dateFirstUsed,
            'date_last_used' => $dateFirstUsed ? $this->faker->dateTimeBetween($dateFirstUsed, 'now') : null,
            'cost' => $this->faker->randomFloat(2, 15, 50),
            'user_id' => User::factory(),
        ];
    }
}
