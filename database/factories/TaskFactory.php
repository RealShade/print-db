<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition() : array
    {
        return [
            'external_id'       => $this->faker->numberBetween(0, 1000000),
            'name'              => $this->faker->words(3, true),
            'count_set_planned' => $this->faker->numberBetween(1, 100),
            'status'            => TaskStatus::NEW,
            'completed_at'      => null,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
