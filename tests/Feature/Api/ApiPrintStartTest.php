<?php

namespace Tests\Feature\Api;

use App\Enums\PrintJobStatus;
use App\Helpers\FilenamePlaceholderHelper;
use App\Models\Part;
use App\Models\PartTask;
use App\Models\PrintJob;
use App\Models\PrintJobPartTask;
use App\Models\Task;

class ApiPrintStartTest extends TestCase
{

    public function test_api_print_start_success_no_data() : void
    {
        $this->createPrinter();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-start');

        $response->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => PrintJobStatus::PRINTING->value,
                    'filename'   => 'undefined',
                    'id'         => 1
                ],
                'tasks'     => [
                    'success' => false,
                    'errors'  => [
                        'filename' => true,
                    ],
                ],
                'slots'     => null
            ]);
        $this->assertDatabaseMissing(app(PrintJobPartTask::class)->getTable(), [
            'print_job_id' => 1
        ]);
    }

    public function test_api_print_start_fails_no_printer() : void
    {
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
        $this->createPrinter();

        $filename = $this->faker()->name . '.gcode';
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-start', ['filename' => $filename]);

        $response->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => PrintJobStatus::PRINTING->value,
                    'filename'   => $filename,
                    'id'         => 1
                ],
                'tasks'     => [
                    'success' => false,
                    'errors'  => [
                        'filename' => true,
                    ],
                ],
                'slots'     => null
            ]);
        $this->assertDatabaseMissing(app(PrintJobPartTask::class)->getTable(), [
            'print_job_id' => 1
        ]);
    }

    public function test_api_print_start_fails_other_printer() : void
    {
        $this->createPrinterOther();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => 'filename',
            'printer_id' => $this->printerOther->id,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors'  => [
                    'printer_id' => true,
                ],
            ]);
        $this->assertDatabaseMissing(app(PrintJob::class)->getTable(), [
            'printer_id' => $this->printerOther->id,
        ]);
    }

    public function test_api_print_start_fails_wrong_printer() : void
    {
        $this->createPrinter();

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
        $this->assertDatabaseMissing(app(PrintJob::class)->getTable(), [
            'printer_id' => $this->printer->id,
        ]);
    }

    public function test_api_print_start_success_with_wrong_task() : void
    {
        $this->createPrinter();
        $this->createTask();

        $filename = FilenamePlaceholderHelper::generate($this->task);
        $this->task->forceDelete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => $filename,
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => PrintJobStatus::PRINTING->value,
                    'filename'   => $filename,
                    'id'         => 1
                ],
                'tasks'     => [
                    'success' => false,
                    'errors'  => [
                        'tasks' => [
                            1 => true
                        ],
                    ],
                ],
                'slots'     => null
            ]);
        $this->assertDatabaseMissing(app(PrintJobPartTask::class)->getTable(), [
            'print_job_id' => 1
        ]);
    }

    public function test_api_print_start_success_with_other_task() : void
    {
        $this->createPrinter();
        $this->createTaskOther();
        $filename = FilenamePlaceholderHelper::generate($this->taskOther);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => $filename,
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => PrintJobStatus::PRINTING->value,
                    'filename'   => $filename,
                    'id'         => 1
                ],
                'tasks'     => [
                    'success' => false,
                    'errors'  => [
                        'tasks' => [
                            1 => true
                        ],
                    ],
                ],
                'slots'     => null
            ]);
        $this->assertDatabaseMissing(app(PrintJobPartTask::class)->getTable(), [
            'print_job_id' => 1
        ]);
    }

    public function test_api_print_start_success_tasks_without_parts() : void
    {
        $this->createPrinter();
        $this->createTask();
        $countPartPrinting = $this->faker()->numberBetween(1, 10);
        $countSetPlanned   = $this->task->count_set_planned;

        $filename = FilenamePlaceholderHelper::generate($this->task, null, $countPartPrinting);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => $filename,
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonMissing([
                'tasks' => [
                    1 => [
                        'parts',
                    ],
                ],
            ])
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => PrintJobStatus::PRINTING->value,
                    'filename'   => $filename,
                    'id'         => 1,
                ],
                'tasks'     => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => [
                                $this->task->id => [
                                    'count_set_planned'  => $countSetPlanned,
                                    'count_set_printed'  => 0,
                                    'count_set_printing' => 0,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        $this->assertDatabaseMissing(app(PrintJobPartTask::class)->getTable(), [
            'print_job_id' => 1,
        ]);
    }

    public function test_api_print_start_success_tasks_with_unlinked_parts() : void
    {
        $this->createPrinter();
        $this->createTask();
        $this->createPart();
        $count        = $this->faker()->numberBetween(1, 10);
        $countPlanned = $this->task->count_set_planned;

        $filename = FilenamePlaceholderHelper::generate($this->task, $this->part, $count);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => $filename,
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => PrintJobStatus::PRINTING->value,
                    'filename'   => $filename,
                    'id'         => 1
                ],
                'tasks'     => [
                    'success' => false,
                    'errors'  => [
                        'parts' => true,
                    ],
                ],
            ]);
        $this->assertDatabaseMissing(app(PrintJobPartTask::class)->getTable(), [
            'print_job_id' => 1
        ]);
    }

    public function test_api_print_start_success_tasks_with_other_parts() : void
    {
        $this->createPrinter();
        $this->createUserOther();
        $this->createTaskWithParts();
        $this->createPart();
        $this->createPartOther();
        $countPartPrinting        = $this->faker()->numberBetween(1, 10);

        $filename = FilenamePlaceholderHelper::generate($this->task, $this->partOther, $countPartPrinting);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => $filename,
            'printer_id' => $this->printer->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => PrintJobStatus::PRINTING->value,
                    'filename'   => $filename,
                    'id'         => 1
                ],
                'tasks'     => [
                    'success' => false,
                    'errors'  => [
                        'parts' => true,
                    ],
                ],
            ]);
        $this->assertDatabaseMissing(app(PrintJobPartTask::class)->getTable(), [
            'print_job_id' => 1
        ]);
    }

    public function test_api_print_start_success_tasks_with_parts(bool $isAuto = false) : void
    {
        $this->createPrinter();

        // Массивы для хранения параметров задач и частей
        $countSetPlanned   = [];
        $countSetPrinting  = [];
        $countPartPerSet   = [];
        $countPartPrinted  = [];
        $countPartPrinting = [];

        // Подготовка данных для 2-х задач, по 2 части в каждой
        for ($i = 0; $i < 2; $i++) {
            $countSetPlanned[ $i ] = $this->faker()->numberBetween(1, 10);
            $countSetPrinting[ $i ]  = $this->faker()->numberBetween(1, 10);
        }

        // Подготовка данных для 4-х частей (по 2 части на задачу)
        for ($i = 0; $i < 4; $i++) {
            $countPartPerSet[ $i ]   = $this->faker()->numberBetween(1, 10);
            $countPartPrinted[ $i ]  = $this->faker()->numberBetween(1, 10);
            $countPartPrinting[ $i ] = $this->faker()->numberBetween(1, 10);
        }

        // Создание двух задач
        $tasks      = [];
        $parts      = [];
        $partsTasks = [];
        $filename   = '';

        for ($i = 0; $i < 2; $i++) {
            $tasks[ $i ] = Task::factory()->create([
                'user_id'           => $this->userActive->id,
                'count_set_planned' => $countSetPlanned[ $i ],
            ]);

            if ($isAuto) {
                $filename .= FilenamePlaceholderHelper::generate($tasks[ $i ], null, $countSetPrinting[ $i ]);
            }
            // Создание двух частей для текущей задачи
            for ($j = 0; $j < 2; $j++) {
                $partIndex           = $i * 2 + $j;
                $parts[ $partIndex ] = Part::factory()->create([
                    'user_id' => $this->userActive->id,
                ]);

                // Привязка части к задаче
                $tasks[ $i ]->parts()->attach($parts[ $partIndex ]->id, [
                    'count_per_set' => $countPartPerSet[ $partIndex ],
                    'count_printed' => $countPartPrinted[ $partIndex ],
                ]);

                // Получить объект связи
                $partTask                 = $tasks[ $i ]->parts()->where('part_id', $parts[ $partIndex ]->id)->first();
                $partsTasks[ $partIndex ] = $partTask;

                // Формирование имени файла
                if (!$isAuto) {
                    $filename .= FilenamePlaceholderHelper::generate($tasks[ $i ], $partTask, $countPartPrinting[ $partIndex ]);
                } else {
                    $countPartPrinting[ $partIndex ] = $countSetPrinting[ $i ] * $countPartPerSet[ $partIndex ];
                }
            }
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-start', [
            'filename'   => $filename,
            'printer_id' => $this->printer->id,
        ]);

        // Подготовка к проверке: создаем ожидаемую структуру ответа
        $expectedTasksData = [];
        for ($i = 0; $i < 2; $i++) {
            $taskId    = $tasks[ $i ]->id;
            $taskParts = [];

            for ($j = 0; $j < 2; $j++) {
                $partIndex            = $i * 2 + $j;
                $partId               = $parts[ $partIndex ]->id;
                $taskParts[ $partId ] = [
                    'part_task_id'   => $partsTasks[ $partIndex ]->pivot->id,
                    'is_printing'    => true,
                    'count_per_set'  => $countPartPerSet[ $partIndex ],
                    'count_required' => $countSetPlanned[ $i ] * $countPartPerSet[ $partIndex ],
                    'count_printed'  => $countPartPrinted[ $partIndex ],
                    'count_printing' => $countPartPrinting[ $partIndex ],
                ];
            }

            // Расчет количества напечатанных и печатающихся комплектов
            $countPrinted  = min(
                (int)($countPartPrinted[ $i * 2 ] / $countPartPerSet[ $i * 2 ]),
                (int)($countPartPrinted[ $i * 2 + 1 ] / $countPartPerSet[ $i * 2 + 1 ])
            );
            $countPrinting = min(
                (int)($countPartPrinting[ $i * 2 ] / $countPartPerSet[ $i * 2 ]),
                (int)($countPartPrinting[ $i * 2 + 1 ] / $countPartPerSet[ $i * 2 + 1 ])
            );

            $expectedTasksData[ $taskId ] = [
                'count_set_planned'  => $countSetPlanned[ $i ],
                'count_set_printed'  => $countPrinted,
                'count_set_printing' => $countPrinting,
                'parts'              => $taskParts,
            ];
        }

        $response->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => PrintJobStatus::PRINTING->value,
                    'filename'   => $filename,
                    'id'         => 1,
                ],
                'tasks'     => [
                    'success' => true,
                    'data'    => [
                        'old' => [
                            'tasks' => $expectedTasksData,
                        ],
                    ],
                ],
            ]);

        // Проверка создания записей в БД для всех частей
        for ($i = 0; $i < 4; $i++) {
            $this->assertDatabaseHas(app(PrintJobPartTask::class)->getTable(), [
                'print_job_id'  => 1,
                'part_task_id'  => $partsTasks[ $i ]->pivot->id,
                'count_printed' => $countPartPrinting[ $i ],
            ]);

            $this->assertDatabaseHas(app(PartTask::class)->getTable(), [
                'id'            => $partsTasks[ $i ]->pivot->id,
                'task_id'       => $tasks[ intdiv($i, 2) ]->id,
                'part_id'       => $parts[ $i ]->id,
                'count_per_set' => $countPartPerSet[ $i ],
                'count_printed' => $countPartPrinted[ $i ],
            ]);
        }
    }

    public function test_api_print_start_success_tasks_with_auto_parts() : void {
        $this->test_api_print_start_success_tasks_with_parts(true);
    }
}
