<?php

namespace Tests\Feature\Api;

use App\Enums\PrintJobStatus;
use App\Helpers\FilenamePlaceholderHelper;
use App\Models\Part;
use App\Models\Printer;
use App\Models\PrintJob;
use App\Models\Task;
use App\Services\PrinterService;

class ApiPrintStopTest extends TestCase
{

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
        $this->createPrinterOther();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-stop', [
            'printer_id' => $this->printerOther->id,
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
        $this->createPrinter();
        /** @var Part $part1 */
        [$part1, $part2] = $this->createTaskWithParts();
        $filename = FilenamePlaceholderHelper::generate($part1->pivot->task, $part1, 3);
        $printJob = PrinterService::getActivePrintJob($this->printer, $filename, true);
        $countPrinted1 = $part1->pivot->count_printed;
        $countPrinted2 = $part2->pivot->count_printed;
        $countSetPrinted = $part1->pivot->task->count_set_printed;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken->token,
        ])->post('/api/print-stop', [
            'printer_id' => $this->printer->id,
        ]);
        $part1->refresh();
        $part2->refresh();

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'printer' => [
                    'id'   => 1,
                    'name' => $this->printer->name,
                ],
                'print_jobs' => [
                    $printJob->id => [
                        'printer_id' => $this->printer->id,
                        'status'     => PrintJobStatus::CANCELLED->value,
                        'filename'   => $filename,
                        'id'         => 1,
                    ],
                ],
                'message' => __('printer.printing_tasks_purged'),
            ]);
        $this->assertDatabaseMissing(app(PrintJob::class)->getTable(), [
            'printer_id' => $this->printer->id,
            'status'     => PrintJobStatus::PRINTING->value,
        ]);
        $this->assertDatabaseHas(app(PrintJob::class)->getTable(), [
            'printer_id' => $this->printer->id,
            'status'     => PrintJobStatus::CANCELLED->value,
        ]);
        $this->assertEquals($countSetPrinted, $part1->pivot->task->count_set_printed);
        $this->assertEquals($countPrinted1, $part1->pivot->count_printed);
        $this->assertEquals($countPrinted2, $part2->pivot->count_printed);
    }
}
