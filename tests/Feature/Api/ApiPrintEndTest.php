<?php

namespace Tests\Feature\Api;

use App\Enums\PrintJobStatus;
use App\Helpers\FilenamePlaceholderHelper;
use App\Models\FilamentSpool;
use App\Models\Part;
use App\Models\PrinterFilamentSlot;
use App\Models\PrintJob;
use App\Models\PrintJobPartTask;
use App\Models\PrintJobSpool;
use App\Models\Task;

class ApiPrintEndTest extends TestCase
{

    /* **************************************** Tests **************************************** */
    public function test_api_print_end_slots_fails_slots_other()
    {
        $this->createPrinter();
        $this->createPrinterOther();
        $slot1        = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printerOther->id,
        ]);
        $slot2        = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printerOther->id,
        ]);

        $filename = 'undefined';
        $weight1  = $this->faker->randomFloat(4, 1, 300);
        $weight2  = $this->faker->randomFloat(4, 1, 300);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end', [
            'slots' => [
                $slot1->attribute => $weight1,
                $slot2->attribute => $weight2,
            ],
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
                    'status'     => PrintJobStatus::COMPLETED->value,
                    'filename'   => $filename,
                    'id'         => 1,
                ],
                'slots'     => [
                    'success' => false,
                    'errors'  => [
                        $slot1->attribute => true,
                        $slot2->attribute => true,
                    ],
                    'data' => []
                ],
            ]);
    }

    public function test_api_print_end_slots_fails_value_not_numeric()
    {
        $this->createPrinter();
        $slot1 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printer->id,
        ]);
        $slot2 = PrinterFilamentSlot::factory()->create([
            'printer_id' => $this->printer->id,
        ]);

        $filename = 'undefined';
        $weight1 = 'z';
        $weight2 = $this->faker->randomFloat(4, 1, 300);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end', [
            'slots' => [
                $slot1->attribute => $weight1,
                $slot2->attribute => $weight2,
            ],
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
                    'status'     => PrintJobStatus::COMPLETED->value,
                    'filename'   => $filename,
                    'id'         => 1,
                ],
                'slots'     => [
                    'success' => false,
                    'errors'  => [
                        $slot1->attribute => true,
                    ],
                    'data' => [
                        'input' => [
                            $slot2->attribute => $weight2,
                        ],
                        'old'   => [
                            $slot2->attribute => [
                                'filament_spool_id' => null,
                                'weight_initial'    => null,
                                'weight_remaining'  => null,
                                'weight_used'       => null,
                            ],
                        ],

                    ]
                ],
            ]);
    }

    public function test_api_print_end_slots_fails_slots_missing()
    {
        $this->createPrinter();

        $filename = 'undefined';
        $weight1 = $this->faker->randomFloat(4, 1, 300);
        $weight2 = $this->faker->randomFloat(4, 1, 300);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end', [
            'slots' => [
                "slot 1" => $weight1,
                "slot 2" => $weight2,
            ],
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
                    'status'     => PrintJobStatus::COMPLETED->value,
                    'filename'   => $filename,
                    'id'         => 1,
                ],
                'slots'     => [
                    'success' => false,
                    'errors'  => [
                        'slot 1' => true,
                        'slot 2' => true,
                    ],
                    'data' => []
                ],
            ]);
    }

    public function test_api_print_end_success_slots_with_empty()
    {
        $this->createPrinter();

        $filamentSpool1 = FilamentSpool::factory()->create([
            'user_id' => $this->userActive->id,
        ]);

        $slot1    = PrinterFilamentSlot::factory()->create([
            'printer_id'        => $this->printer->id,
            'filament_spool_id' => $filamentSpool1->id,
        ]);
        $slot2    = PrinterFilamentSlot::factory()->create([
            'printer_id'        => $this->printer->id,
            'filament_spool_id' => null,
        ]);
        $weight1     = $this->faker->randomFloat(4, 1, 300);
        $weight2     = $this->faker->randomFloat(4, 1, 300);
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

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->apiToken->token])->post('/api/print-end', [
            'slots' => [
                $slot1->attribute => $weight1,
                $slot2->attribute => $weight2,
            ],
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
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'slots'   => [
                    'success' => true,
                    'errors'  => [],
                    'data'    => [
                        'input' => [
                            $slot1->attribute => $weight1,
                            $slot2->attribute => $weight2,
                        ],
                        'old'   => $oldData,
                        'new'   => $newData,
                    ],
                ],
            ]);
        $filamentSpool1->refresh();
        $this->assertEquals(round($oldUsed1 + $weight1, 4), $filamentSpool1->weight_used);
    }

    public function test_api_print_end_success_tasks_with_parts() : void
    {
        $this->print_end_success(false);
    }

    public function test_api_print_end_success_tasks_with_auto_parts() : void
    {
        $this->print_end_success(true);
    }

    protected function print_end_success(string $isAutoPart) : void
    {
        /** @var Task $task1 */
        /** @var Task $task2 */
        /** @var Part $part1 */
        /** @var Part $part2 */
        /** @var Part $part3 */
        /** @var Part $part4 */
        [$task1, $task2, $part1, $part2, $part3, $part4, $data] = $this->createTasksWithParts();

        $this->createPrinter();

        if ($isAutoPart) {
            $filename = FilenamePlaceholderHelper::generate($task1, null, $data[ $task1->id ]['count'])
                . FilenamePlaceholderHelper::generate($task2, null, $data[ $task2->id ]['count'])
                . '.gcode';
        } else {
            $filename = FilenamePlaceholderHelper::generate($task1, $part1, $data[ $task1->id ]['parts'][$part1->id]['count'])
                . FilenamePlaceholderHelper::generate($task1, $part2, $data[ $task1->id ]['parts'][$part2->id]['count'])
                . FilenamePlaceholderHelper::generate($task2, $part3, $data[ $task2->id ]['parts'][$part3->id]['count'])
                . FilenamePlaceholderHelper::generate($task2, $part4, $data[ $task2->id ]['parts'][$part4->id]['count'])
                . '.gcode';
        }

        $q = [];
        foreach ($data as $taskRow) {
            /** @var Task $task */
            $task = $taskRow['task'];
            $q['old']['tasks'][$task->id] = [
                'count_set_planned'  => $task->count_set_planned,
                'count_set_printed'  => $task->count_set_printed,
                'count_set_printing' => $isAutoPart ? $data[$task->id]['count']
                    : $task['parts']
                        ->map(fn($part) => (int)($data[$task->id]['parts'][$part->id]['count'] / $part->pivot->count_per_set))
                        ->min() ?? 0,
            ];
            foreach ($taskRow['parts'] as $partRow) {
                /** @var Part $part */
                $part = $partRow['part'];
                $q['old']['tasks'][$task->id]['parts'][$part->id] = [
                    'part_task_id'   => $part->id,
                    'is_printing'    => true,
                    'count_per_set'  => $part->pivot->count_per_set,
                    'count_required' => $task->count_set_planned * $part->pivot->count_per_set,
                    'count_printed'  => $part->pivot->count_printed,
                    'count_printing' => $isAutoPart ? $data[$task->id]['count'] * $part->pivot->count_per_set : $data[$task->id]['parts'][$part->id]['count'],
                ];
            }
            $q['new']['tasks'][$task->id] = [
                'count_set_planned'  => $task->count_set_planned,
                'count_set_printed'  => collect($q['old']['tasks'][$task->id]['parts'])
                        ->map(fn($part) => (int)(($part['count_printed'] + $part['count_printing']) / $part['count_per_set']))
                        ->min() ?? 0,
                'count_set_printing' => 0,
            ];
            foreach ($taskRow['parts'] as $partRow) {
                /** @var Part $part */
                $part = $partRow['part'];
                $q['new']['tasks'][$task->id]['parts'][$part->id] = [
                    'part_task_id'   => $part->id,
                    'count_per_set'  => $part->pivot->count_per_set,
                    'count_required' => $task->count_set_planned * $part->pivot->count_per_set,
                    'count_printed'  => $q['old']['tasks'][$task->id]['parts'][$part->id]['count_printed'] + $q['old']['tasks'][$task->id]['parts'][$part->id]['count_printing'],
                    'count_printing' => 0,
                ];
            }
        }

        $filamentSlot = [];
        foreach (range(0, 1) as $id) {
            $filamentSlot[$id] = PrinterFilamentSlot::factory()->create([
                'printer_id'        => $this->printer->id,
            ]);
        }
        $filamentSpool = [];
        foreach (range(0, 1) as $id) {
            $filamentSpool[$id] = FilamentSpool::factory()->create([
                'user_id' => $this->userActive->id,
            ]);
        }

        $slots = [];
        foreach (range(0, 1) as $id) {
            $slots[$filamentSlot[$id]->attribute] = $this->faker->randomFloat(4, 1, 300);
            $filamentSlot[$id]->update([
                'filament_spool_id' => $filamentSpool[$id]->id,
            ]);
        }

        $q2 = [];
        foreach (range(0, 1) as $id) {
            $q2['input'][ $filamentSlot[ $id ]->attribute ] = $slots[ $filamentSlot[ $id ]->attribute ];
            $q2['old'][ $filamentSlot[ $id ]->attribute ]   = [
                'filament_spool_id' => $filamentSpool[ $id ]->id,
                'weight_initial'    => $filamentSpool[ $id ]->weight_initial,
                'weight_remaining'  => $filamentSpool[ $id ]->weight_remaining,
                'weight_used'       => $filamentSpool[ $id ]->weight_used,
            ];
            $q2['new'][ $filamentSlot[ $id ]->attribute ]   = [
                'filament_spool_id' => $filamentSpool[ $id ]->id,
                'weight_initial'    => $filamentSpool[ $id ]->weight_initial,
                'weight_remaining'  => number_format($filamentSpool[ $id ]->weight_remaining - $slots[ $filamentSlot[ $id ]->attribute ], 4, '.', ''),
                'weight_used'       => number_format($filamentSpool[ $id ]->weight_used + $slots[ $filamentSlot[ $id ]->attribute ], 4, '.', ''),
            ];
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-end', [
            'filename'   => $filename,
            'printer_id' => $this->printer->id,
            'slots'      => $slots,
        ]);

        $printJob = PrintJob::where('printer_id', $this->printer->id)
            ->first();

        $response->assertStatus(200)
            ->assertJson([
                'success'   => true,
                'printer'   => [
                    'id'   => $this->printer->id,
                    'name' => $this->printer->name,
                ],
                'print_job' => [
                    'printer_id' => $this->printer->id,
                    'status'     => $printJob->status->value,
                    'filename'   => $printJob->filename,
                    'id'         => $printJob->id,
                ],
                'tasks'     => [
                    'success' => true,
                    'data'    => $q,
                ],
                'slots'     => [
                    'success' => true,
                    'errors'  => [],
                    'data'    => $q2,
                ],
            ]);

        foreach ($data as $taskRow) {
            foreach ($taskRow['parts'] as $partRow) {
                $this->assertDatabaseHas(app(PrintJobPartTask::class)->getTable(), [
                    'print_job_id' => $printJob->id,
                    'part_task_id' => $partRow['part']->id,
                    'count_printed' => $q['old']['tasks'][$taskRow['task']->id]['parts'][$partRow['part']->id]['count_printing'],
                ]);
            }
        }

        $this->assertDatabaseCount(app(PrintJob::class)->getTable(), 1);
        $this->assertDatabaseHas(app(PrintJob::class)->getTable(), [
            'printer_id' => $this->printer->id,
            'status'     => PrintJobStatus::COMPLETED->value,
        ]);
        $this->assertDatabaseMissing(app(PrintJob::class)->getTable(), [
            'status'     => PrintJobStatus::PRINTING->value,
        ]);
        foreach (range(0, 1) as $id) {
            $this->assertDatabaseHas(app(PrintJobSpool::class)->getTable(), [
                'print_job_id'      => $printJob->id,
                'filament_spool_id' => $filamentSpool[ $id ]->id,
                'weight_used'       => number_format($slots[ $filamentSlot[ $id ]->attribute ], 4, '.', ''),
        ]);
        }
    }
}
