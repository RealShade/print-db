<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Helpers\FilenamePlaceholderHelper;
use App\Models\ApiToken;
use App\Models\FilamentSpool;
use App\Models\Part;
use App\Models\Printer;
use App\Models\PrinterFilamentSlot;
use App\Models\PrintingTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User     $activeUser;
    protected User     $otherUser;
    protected ApiToken $apiToken;
    protected Printer  $printer;

    /* **************************************** Tests **************************************** */
    public function test_api_auth_fails_no_token() : void
    {
        $response = $this->withHeaders([
        ])->post('/api');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_api_auth_fails_with_empty_token()
    {
        $response = $this->getJson('/api', ['Authorization' => 'Bearer ']);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_api_auth_fails_with_invalid_token()
    {
        $response = $this->getJson('/api', ['Authorization' => 'Bearer invalid_token']);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_api_auth_succeeds_with_valid_token()
    {
        $token = ApiToken::factory()->create(['user_id' => $this->activeUser->id]);

        $response = $this->getJson('/api', ['Authorization' => 'Bearer ' . $token->token]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'profile'  => [
                        'id'    => $this->activeUser->id,
                        'name'  => $this->activeUser->name,
                        'email' => $this->activeUser->email,
                    ],
                    'printers' => [
                        [
                            'id'   => $this->printer->id,
                            'name' => $this->printer->name,
                        ],
                    ],
                ],
            ]);
    }

    public function test_api_auth_fails_for_blocked_user()
    {
        $user = User::factory()->create(['status' => UserStatus::BLOCKED]);

        $token = ApiToken::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api', ['Authorization' => 'Bearer ' . $token->token]);

        $response->assertStatus(403)
            ->assertJson(['error' => __('auth.account_inactive')]);
    }

    public function test_api_auth_fails_for_deleted_user()
    {
        $user = User::factory()->create(['status' => UserStatus::BLOCKED]);

        $token = ApiToken::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api', ['Authorization' => 'Bearer ' . $token->token]);

        $response->assertStatus(403)
            ->assertJson(['error' => __('auth.account_inactive')]);
    }

    public function test_api_print_start_fails_no_data() : void
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-start');

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => false,
                    'errors'  => [
                        'filename' => true,
                    ],
                ],
            ]);
    }

    public function test_api_print_start_fails_no_printer() : void
    {
        $this->printer->delete();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-start', ['filename' => 'filename']);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors'  => [
                    'printer_id' => true,
                ],
            ]);
    }

    public function test_api_print_start_success_no_template() : void
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-start', ['filename' => 'filename']);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => false,
                    'errors'  => [
                        'filename' => true,
                    ],
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

        $response->assertStatus(422)
            ->assertJson([
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

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors'  => [
                    'printer_id' => true,
                ],
            ]);
    }

    public function test_api_print_start_fails_wrong_task() : void
    {
        $Task = Task::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $filename = FilenamePlaceholderHelper::generate($Task);
        $Task->forceDelete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => $filename,
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => false,
                    'errors'  => [
                        'tasks' => true,
                    ],
                ],
            ]);
    }

    public function test_api_print_start_fails_other_task() : void
    {
        $Task = Task::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($Task),
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => false,
                    'errors'  => [
                        'tasks' => true,
                    ],
                ],
            ]);
    }

    public function test_api_print_start_success_tasks_without_parts() : void
    {
        $Task = Task::factory()->create([
            'user_id' => $this->activeUser->id,
            'count_set_planned' => 10,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($Task, null, 5),
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonMissing([
                'tasks' => [
                    'data' => [
                        'tasks' => [
                            1 => [
                                'parts',
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 0,
                                    'count_set_printing' => 0,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        $this->assertDatabaseMissing(app(PrintingTask::class)->getTable(), [
            'printer_id' => $this->printer->id,
        ]);
    }

    public function test_api_print_start_fails_tasks_with_unlinked_parts() : void
    {
        $task = Task::factory()->create([
            'user_id'           => $this->activeUser->id,
            'count_set_planned' => 10,
        ]);

        $part = Part::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => FilenamePlaceholderHelper::generate($task, $part, 5),
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => false,
                    'errors'  => [
                        'parts' => true,
                    ],
                ],
            ]);
    }

    public function test_api_print_start_fails_tasks_with_other_parts() : void
    {
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
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => false,
                    'errors'  => [
                        'parts' => true,
                    ],
                ],
            ]);
    }

    public function test_api_print_start_success_tasks_with_parts() : void
    {
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
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 1,
                                    'count_set_printing' => 2,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'is_printing'    => true,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 2,
                                            'count_printing' => 5,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        $this->assertDatabaseHas(app(PrintingTask::class)->getTable(), [
            'printer_id' => $this->printer->id,
        ]);
    }

    public function test_api_print_end_success_tasks_with_parts() : void
    {
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
        ])->post('/api/print-end', [
            'filename'   => FilenamePlaceholderHelper::generate($task, $part, 5),
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 1,
                                    'count_set_printing' => 2,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'is_printing'    => true,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 2,
                                            'count_printing' => 5,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'new' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 3,
                                    'count_set_printing' => 0,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 7,
                                            'count_printing' => 0,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        $this->assertDatabaseMissing(app(PrintingTask::class)->getTable(), [
            'printer_id' => $this->printer->id,
        ]);
    }

    public function test_api_print_start_success_tasks_with_auto_parts() : void
    {
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
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 1,
                                    'count_set_printing' => 5,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'is_printing'    => true,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 2,
                                            'count_printing' => 10,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        $this->assertDatabaseHas(app(PrintingTask::class)->getTable(), [
            'printer_id' => $this->printer->id,
        ]);
    }

    public function test_api_print_end_success_tasks_with_auto_parts() : void
    {
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
        ])->post('/api/print-end', [
            'filename'   => FilenamePlaceholderHelper::generate($task, null, 5),
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 1,
                                    'count_set_printing' => 5,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'is_printing'    => true,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 2,
                                            'count_printing' => 10,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'new' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 6,
                                    'count_set_printing' => 0,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 12,
                                            'count_printing' => 0,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        $this->assertDatabaseMissing(app(PrintingTask::class)->getTable(), [
            'printer_id' => $this->printer->id,
        ]);
    }

    public function test_api_print_start_success_tasks_with_auto_multi_parts() : void
    {
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
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 1,
                                    'count_set_printing' => 5,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'is_printing'    => true,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 2,
                                            'count_printing' => 10,
                                        ],
                                        2 => [
                                            'part_task_id'   => 2,
                                            'is_printing'    => true,
                                            'count_per_set'  => 3,
                                            'count_required' => 30,
                                            'count_printed'  => 3,
                                            'count_printing' => 15,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        $this->assertDatabaseCount(app(PrintingTask::class)->getTable(), 2);
    }

    public function test_api_print_end_success_tasks_with_auto_multi_parts() : void
    {
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
        ])->post('/api/print-end', [
            'filename'   => FilenamePlaceholderHelper::generate($task, null, 5),
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'tasks'   => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 1,
                                    'count_set_printing' => 5,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'is_printing'    => true,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 2,
                                            'count_printing' => 10,
                                        ],
                                        2 => [
                                            'part_task_id'   => 2,
                                            'is_printing'    => true,
                                            'count_per_set'  => 3,
                                            'count_required' => 30,
                                            'count_printed'  => 3,
                                            'count_printing' => 15,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'new' => [
                            'tasks' => [
                                1 => [
                                    'count_set_planned'  => 10,
                                    'count_set_printed'  => 6,
                                    'count_set_printing' => 0,
                                    'parts'              => [
                                        1 => [
                                            'part_task_id'   => 1,
                                            'count_per_set'  => 2,
                                            'count_required' => 20,
                                            'count_printed'  => 12,
                                            'count_printing' => 0,
                                        ],
                                        2 => [
                                            'part_task_id'   => 2,
                                            'count_per_set'  => 3,
                                            'count_required' => 30,
                                            'count_printed'  => 18,
                                            'count_printing' => 0,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        $this->assertDatabaseMissing(app(PrintingTask::class)->getTable(), [
            'printer_id' => $this->printer->id,
        ]);
    }


    public function test_api_print_stop_fails_no_printer_set()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-stop', [
        ]);

        $response->assertStatus(422)
            ->assertJson([
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

        $response->assertStatus(422)
            ->assertJson([
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

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors'  => [
                    'printer_id' => true,
                ],
            ]);
    }

    public function test_api_print_stop_success()
    {
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
        $this->printer->printingTasks()->create([
            'part_task_id' => 1,
            'count'        => 5,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-stop', [
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'printer' => [
                    1 => $this->printer->name,
                ],
                'message' => __('printer.printing_tasks_purged'),
            ]);
        $this->assertDatabaseMissing(app(PrintingTask::class)->getTable(), [
            'printer_id' => $this->printer->id,
        ]);
        $this->assertEquals(1, $task->getCompletedSetsCount());
        $this->assertEquals(2, $task->parts->first()->pivot->count_printed);
    }

    public function test_api_print_end_fails_slots_missing()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end',[
            'slots' => [
                "slot 1" => 1,
                "slot 2" => 2,
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'slots'   => [
                    'success' => false,
                    'errors'  => [
                        'slot 1' => true,
                        'slot 2' => true,
                    ],
                ],
            ]);
    }

    public function test_api_print_end_fails_slots_other()
    {
        $otherPrinter = Printer::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);
        $slot1 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $otherPrinter,
        ]);
        $slot2 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $otherPrinter,
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end',[
            'slots' => [
                $slot1->attribute => 1,
                $slot2->attribute => 2,
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'slots'   => [
                    'success' => false,
                    'errors'  => [
                        $slot1->attribute => true,
                        $slot2->attribute => true,
                    ],
                ],
            ]);
    }

    public function test_api_print_end_fails_slots_value_not_numeric()
    {
        $slot1 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printer->id,
        ]);
        $slot2 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printer->id,
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end',[
            'slots' => [
                $slot1->attribute => 'z',
                $slot2->attribute => 2,
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'slots'   => [
                    'success' => false,
                    'errors'  => [
                        $slot1->attribute => true,
                    ],
                ],
            ]);
    }

    public function test_api_print_end_success_slots()
    {
        $filamentSpool1 = FilamentSpool::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);
        $filamentSpool2 = FilamentSpool::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $slot1 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printer->id,
            'filament_spool_id' => $filamentSpool1->id,
        ]);
        $slot2 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printer->id,
            'filament_spool_id' => $filamentSpool2->id,
        ]);
        $sub1 = $this->faker->randomFloat(4, 1, 300);
        $sub2 = $this->faker->randomFloat(4, 1, 300);

        $oldData = [
            $slot1->attribute => [
                'filament_spool_id' => $filamentSpool1->id,
                'weight_initial'    => $filamentSpool1->weight_initial,
                'weight_remaining'  => $filamentSpool1->weight_remaining,
                'weight_used'       => $filamentSpool1->weight_used,
            ],
            $slot2->attribute => [
                'filament_spool_id' => $filamentSpool2->id,
                'weight_initial'    => $filamentSpool2->weight_initial,
                'weight_remaining'  => $filamentSpool2->weight_remaining,
                'weight_used'       => $filamentSpool2->weight_used,
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end',[
            'slots' => [
                $slot1->attribute => $sub1,
                $slot2->attribute => $sub2,
            ]
        ]);

        $filamentSpool1->refresh();
        $filamentSpool2->refresh();
        $newData = [
            $slot1->attribute => [
                'filament_spool_id' => $filamentSpool1->id,
                'weight_initial'    => $filamentSpool1->weight_initial,
                'weight_remaining'  => $filamentSpool1->weight_remaining,
                'weight_used'       => $filamentSpool1->weight_used,
            ],
            $slot2->attribute => [
                'filament_spool_id' => $filamentSpool2->id,
                'weight_initial'    => $filamentSpool2->weight_initial,
                'weight_remaining'  => $filamentSpool2->weight_remaining,
                'weight_used'       => $filamentSpool2->weight_used,
            ],
        ];

        $response->assertStatus(200)
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'slots'   => [
                    'success' => true,
                    'errors'  => [],
                    'data'    => [
                        'input' => [
                            $slot1->attribute => $sub1,
                            $slot2->attribute => $sub2,
                        ],
                        'old'   => $oldData,
                        'new'   => $newData,
                    ],
                ],
            ]);
    }

    public function test_api_print_end_success_slots_with_empty()
    {
        $filamentSpool1 = FilamentSpool::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

        $slot1 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printer->id,
            'filament_spool_id' => $filamentSpool1->id,
        ]);
        $slot2 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printer->id,
            'filament_spool_id' => null,
        ]);
        $sub1 = $this->faker->randomFloat(4, 1, 300);
        $sub2 = $this->faker->randomFloat(4, 1, 300);
        $oldUsed1 = $filamentSpool1->weight_used;

        $oldData = [
            $slot1->attribute => [
                'filament_spool_id' => $filamentSpool1->id,
                'weight_initial'    => $filamentSpool1->weight_initial,
                'weight_remaining'  => $filamentSpool1->weight_remaining,
                'weight_used'       => $filamentSpool1->weight_used,
            ],
            $slot2->attribute => [
                'filament_spool_id' => null,
                'weight_initial'    => null,
                'weight_remaining'  => null,
                'weight_used'       => null,
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end',[
            'slots' => [
                $slot1->attribute => $sub1,
                $slot2->attribute => $sub2,
            ]
        ]);

        $filamentSpool1->refresh();
        $newData = [
            $slot1->attribute => [
                'filament_spool_id' => $filamentSpool1->id,
                'weight_initial'    => $filamentSpool1->weight_initial,
                'weight_remaining'  => $filamentSpool1->weight_remaining,
                'weight_used'       => $filamentSpool1->weight_used,
            ],
        ];

        $response->assertStatus(200)
            ->assertJsonMissing([
                'slots' => [
                    'data' => [
                        'slots' => [
                            $slot2->attribute => true,
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'slots'   => [
                    'success' => true,
                    'errors'  => [],
                    'data'    => [
                        'input'  => [
                            $slot1->attribute => $sub1,
                            $slot2->attribute => $sub2,
                        ],
                        'old' => $oldData,
                        'new' => $newData,
                    ],
                ],
            ]);
        $filamentSpool1->refresh();
        $this->assertEquals(round($oldUsed1 + $sub1, 4), $filamentSpool1->weight_used);
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
        $this->printer  = Printer::factory()->create([
            'user_id' => $this->activeUser->id,
        ]);

    }

}
