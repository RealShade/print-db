<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Helpers\FilenamePlaceholderHelper;
use App\Models\ApiToken;
use App\Models\Part;
use App\Models\Printer;
use App\Models\PrintingTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected User     $activeUser;
    protected User     $otherUser;
    protected ApiToken $apiToken;

    /* **************************************** Tests **************************************** */
    public function test_api_print_start_fails_no_data() : void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
        ]);

        //                dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'filename'   => true,
                'printer_id' => true,
            ],
        ]);
    }

    public function test_api_print_start_fails_no_printer() : void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename' => 'filename',
        ]);

        //                dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'printer_id' => true,
            ],
        ]);
    }

    public function test_api_print_start_fails_no_template() : void
    {
        Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename' => 'filename',
        ]);

        //        dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'filename' => true,
            ],
        ]);
    }

    public function test_api_print_start_fails_other_printer() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => 'filename',
            'printer_id' => $printer->id,
        ]);

        //        dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'printer_id' => true,
            ],
        ]);
    }

    public function test_api_print_start_fails_wrong_printer() : void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => 'filename',
            'printer_id' => 999,
        ]);

        //        dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'printer_id' => true,
            ],
        ]);
    }

    public function test_api_print_start_fails_wrong_task() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $Task = Task::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $filename = FilenamePlaceholderHelper::generate($Task);
        $Task->forceDelete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => $filename,
            'printer_id' => $printer->id,
        ]);

        //                dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'tasks' => true,
            ],
        ]);
    }

    public function test_api_print_start_fails_other_task() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $Task = Task::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($Task),
            'printer_id' => $printer->id,
        ]);

        //                dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'tasks' => true,
            ],
        ]);
    }

    public function test_api_print_start_success_tasks_without_parts() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $Task = Task::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($Task),
            'printer_id' => $printer->id,
        ]);

        //        dump($response->json());

        $response->assertStatus(200);
        $response->assertJsonMissing([
            'data' => [
                'tasks' => [
                    1 => [
                        'parts',
                    ],
                ],
            ],
        ]);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'tasks'   => [
                    1 => true,
                ],
                'printer' => [
                    1 => true,
                ],
            ],
        ]);
        $this->assertDatabaseMissing(app(PrintingTask::class)->getTable(), [
            'printer_id' => $printer->id,
        ]);
    }

    public function test_api_print_start_fails_tasks_with_unlinked_parts() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $task = Task::factory()->create([
            'user_id'           => $this->activeUser->id,
            'count_set_planned' => 10,
        ]);

        $part = Part::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        //        $task->parts()->attach($part->id, [
        //            'count_per_set' => 2,
        //            'count_printed' => 2,
        //        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($task, $part, 5),
            'printer_id' => $printer->id,
        ]);

        //        dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'parts' => true,
            ],
        ]);
    }

    public function test_api_print_start_fails_tasks_with_other_parts() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $task = Task::factory()->create([
            'user_id'           => $this->activeUser->id,
            'count_set_planned' => 10,
        ]);

        $part = Part::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $partOther = Part::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        $task->parts()->attach($part->id, [
            'count_per_set' => 2,
            'count_printed' => 2,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($task, $partOther, 5),
            'printer_id' => $printer->id,
        ]);

        //        dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'parts' => true,
            ],
        ]);
    }

    public function test_api_print_start_success_tasks_with_parts() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $task = Task::factory()->create([
            'user_id'           => $this->activeUser->id,
            'count_set_planned' => 10,
        ]);

        $part = Part::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $task->parts()->attach($part->id, [
            'count_per_set' => 2,
            'count_printed' => 2,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($task, $part, 5),
            'printer_id' => $printer->id,
        ]);

        //        dump($response->json());

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'tasks'   => [
                    1 => [
                        'count_set_planned'  => 10,
                        'count_set_printed'  => 1,
                        'count_set_printing' => 2,
                        'count_set_future'   => 3,
                        'parts'              => [
                            1 => [
                                'part_task_id'   => 1,
                                'is_printing'    => true,
                                'count_per_set'  => 2,
                                'count_required' => 20,
                                'count_printed'  => 2,
                                'count_printing' => 5,
                                'count_future'   => 7,
                            ],
                        ],
                    ],
                ],
                'printer' => [
                    1 => true,
                ],
            ],
        ]);
        $this->assertDatabaseHas(app(PrintingTask::class)->getTable(), [
            'printer_id' => $printer->id,
        ]);
    }

    public function test_api_print_start_success_tasks_with_auto_parts() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $task = Task::factory()->create([
            'user_id'           => $this->activeUser->id,
            'count_set_planned' => 10,
        ]);

        $part = Part::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $task->parts()->attach($part->id, [
            'count_per_set' => 2,
            'count_printed' => 2,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($task, null, 5),
            'printer_id' => $printer->id,
        ]);

        //        dump($response->json());

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'tasks'   => [
                    1 => [
                        'count_set_planned'  => 10,
                        'count_set_printed'  => 1,
                        'count_set_printing' => 5,
                        'count_set_future'   => 6,
                        'parts'              => [
                            1 => [
                                'part_task_id'   => 1,
                                'is_printing'    => true,
                                'count_per_set'  => 2,
                                'count_required' => 20,
                                'count_printed'  => 2,
                                'count_printing' => 10,
                                'count_future'   => 12,
                            ],
                        ],
                    ],
                ],
                'printer' => [
                    1 => true,
                ],
            ],
        ]);
    }

    public function test_api_print_start_success_tasks_with_auto_multi_parts() : void
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $task = Task::factory()->create([
            'user_id'           => $this->activeUser->id,
            'count_set_planned' => 10,
        ]);

        $part1 = Part::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);
        $part2 = Part::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $task->parts()->attach($part1->id, [
            'count_per_set' => 2,
            'count_printed' => 2,
        ]);
        $task->parts()->attach($part2->id, [
            'count_per_set' => 3,
            'count_printed' => 3,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($task, null, 5),
            'printer_id' => $printer->id,
        ]);

        //                dump($response->json());

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'tasks'   => [
                    1 => [
                        'count_set_planned'  => 10,
                        'count_set_printed'  => 1,
                        'count_set_printing' => 5,
                        'count_set_future'   => 6,
                        'parts'              => [
                            1 => [
                                'part_task_id'   => 1,
                                'is_printing'    => true,
                                'count_per_set'  => 2,
                                'count_required' => 20,
                                'count_printed'  => 2,
                                'count_printing' => 10,
                                'count_future'   => 12,
                            ],
                            2 => [
                                'part_task_id'   => 2,
                                'is_printing'    => true,
                                'count_per_set'  => 3,
                                'count_required' => 30,
                                'count_printed'  => 3,
                                'count_printing' => 15,
                                'count_future'   => 18,
                            ],
                        ],
                    ],
                ],
                'printer' => [
                    1 => true,
                ],
            ],
        ]);
        $this->assertDatabaseCount(app(PrintingTask::class)->getTable(), 2);
    }


    public function test_api_print_stop_fails_no_printer_set()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-stop', [
        ]);

        //                dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'printer_id' => true,
            ],
        ]);
    }

    public function test_api_print_stop_fails_no_printer_found()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-stop', [
            'printer_id' => 999,
        ]);

        //                dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'printer_id' => true,
            ],
        ]);
    }

    public function test_api_print_stop_fails_other_printer()
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-stop', [
            'printer_id' => $printer->id,
        ]);

        //                dump($response->json());

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors'  => [
                'printer_id' => true,
            ],
        ]);
    }

    public function test_api_print_stop_success()
    {
        $printer = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);
        $part = Part::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);
        $task = Task::factory()->create([
            'user_id'           => $this->activeUser->id,
            'count_set_planned' => 10,
        ]);
        $task->parts()->attach($part->id, [
            'count_per_set' => 2,
            'count_printed' => 2,
        ]);
        $printer->printingTasks()->create([
            'part_task_id' => 1,
            'count'        => 5,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-stop', [
            'printer_id' => $printer->id,
        ]);

        //                dump($response->json());

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'printer' => [
                1 => $printer->name,
            ],
            'message' => __('printer.printing_tasks_purged'),
        ]);
        $this->assertDatabaseMissing(app(PrintingTask::class)->getTable(), [
            'printer_id' => $printer->id,
        ]);
    }

    /* **************************************** Protected **************************************** */
    protected function setUp() : void
    {
        parent::setUp();

        $this->activeUser = User::factory()->create([
            'status' => UserStatus::ACTIVE,
        ]);
        $this->apiToken   = ApiToken::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);
        $this->otherUser  = User::factory()->create([
            'status' => UserStatus::ACTIVE,
        ]);
    }

}
