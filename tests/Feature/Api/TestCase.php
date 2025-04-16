<?php

namespace Tests\Feature\Api;

use App\Enums\UserStatus;
use App\Models\ApiToken;
use App\Models\Part;
use App\Models\Printer;
use App\Models\PrintJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected User $userActive;
    protected User $userOther;

    protected Printer  $printer;
    protected Printer  $printerOther;
    protected ApiToken $apiToken;

    protected PrintJob $printJob;

    protected Task   $task;
    protected Task   $taskOther;
    protected Part   $part;
    protected Part   $partOther;
    protected string $filename;

    /* **************************************** Protected **************************************** */
    protected function createApiToken() : void
    {
        if (isset($this->apiToken)) {
            return;
        }

        $this->createUserActive();
        $this->apiToken = ApiToken::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
    }

    protected function createPart() : void
    {
        if (isset($this->part)) {
            return;
        }

        $this->createUserActive();
        $this->part = Part::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
    }

    protected function createPartOther() : void
    {
        if (isset($this->partOther)) {
            return;
        }

        $this->createUserOther();
        $this->partOther = Part::factory()->create([
            'user_id' => $this->userOther->id,
        ]);
    }

    protected function createPrintJob() : void
    {
        if (isset($this->printJob)) {
            return;
        }

        $this->createPrinter();
        $this->printJob = PrintJob::factory()->create([
            'printer_id' => $this->printer->id,
        ]);
    }

    protected function createPrinter() : void
    {
        if (isset($this->printer)) {
            return;
        }

        $this->createUserActive();
        $this->printer = Printer::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
    }

    protected function createPrinterOther() : void
    {
        if (isset($this->printerOther)) {
            return;
        }

        $this->createUserOther();
        $this->printerOther = Printer::factory()->create([
            'user_id' => $this->userOther->id,
        ]);
    }

    protected function createTask() : void
    {
        if (isset($this->task)) {
            return;
        }

        $this->createUserActive();
        $this->task = Task::factory()->create([
            'user_id' => $this->userActive->id,
            'count_set_planned' => $this->faker->numberBetween(1, 10),
        ]);
    }

    protected function createTaskOther() : void
    {
        if (isset($this->taskOther)) {
            return;
        }

        $this->createUserOther();
        $this->taskOther = Task::factory()->create([
            'user_id' => $this->userOther->id,
        ]);
    }

    protected function createTaskWithParts() : array
    {
        if (!isset($this->task)) {
            $this->createTask();
        }

        $part1 = Part::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
        $part2 = Part::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
        $this->task->parts()->attach($part1, [
            'count_per_set' => $this->faker->numberBetween(1, 10),
            'count_printed' => $this->faker->numberBetween(1, 10),
        ]);
        $this->task->parts()->attach($part2, [
            'count_per_set' => $this->faker->numberBetween(1, 10),
            'count_printed' => $this->faker->numberBetween(1, 10),
        ]);
        $part1 = $this->task->parts[0];
        $part2 = $this->task->parts[1];

        return [$part1, $part2];
    }

    protected function createTasksWithParts() : array
    {
        $task1 = Task::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
        $part1 = Part::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
        $part2 = Part::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
        $part3 = Part::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
        $part4 = Part::factory()->create([
            'user_id' => $this->userActive->id,
        ]);
        $task1->parts()->attach($part1, [
            'count_per_set' => $this->faker->numberBetween(1, 10),
            'count_printed' => $this->faker->numberBetween(1, 10),
        ]);
        $task1->parts()->attach($part2, [
            'count_per_set' => $this->faker->numberBetween(1, 10),
            'count_printed' => $this->faker->numberBetween(1, 10),
        ]);
        $task2->parts()->attach($part3, [
            'count_per_set' => $this->faker->numberBetween(1, 10),
            'count_printed' => $this->faker->numberBetween(1, 10),
        ]);
        $task2->parts()->attach($part4, [
            'count_per_set' => $this->faker->numberBetween(1, 10),
            'count_printed' => $this->faker->numberBetween(1, 10),
        ]);

        return [$task1, $task2, $part1, $part2, $part3, $part4,
            [
                $task1->id => [
                    'task'  => $task1,
                    'count' => $this->faker->numberBetween(1, 50),
                    'parts' => [
                        $task1->parts[0]->id => [
                            'part'  => $task1->parts[0],
                            'count' => $this->faker->numberBetween(1, 50),
                        ],
                        $task1->parts[1]->id => [
                            'part'  => $task1->parts[1],
                            'count' => $this->faker->numberBetween(1, 50),
                        ],
                    ],
                ],
                $task2->id => [
                    'task'  => $task2,
                    'count' => $this->faker->numberBetween(1, 50),
                    'parts' => [
                        $task2->parts[0]->id => [
                            'part'  => $task2->parts[0],
                            'count' => $this->faker->numberBetween(1, 50),
                        ],
                        $task2->parts[1]->id => [
                            'part'  => $task2->parts[1],
                            'count' => $this->faker->numberBetween(1, 50),
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function createUserActive() : void
    {
        if (isset($this->userActive)) {
            return;
        }

        $this->userActive = User::factory()->create([
            'status' => UserStatus::ACTIVE,
        ]);
    }

    protected function createUserOther() : void
    {
        if (isset($this->userOther)) {
            return;
        }

        $this->userOther = User::factory()->create();
    }

    protected function setUp() : void
    {
        parent::setUp();

        $this->filename = $this->faker->word() . '.gcode';
        $this->createApiToken();
    }

}
