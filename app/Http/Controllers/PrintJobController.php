<?php

namespace App\Http\Controllers;

use App\Enums\PrintJobStatus;
use App\Http\Requests\PrintJobRequest;
use App\Models\Printer;
use App\Models\PrintJob;
use App\Services\PrinterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class PrintJobController extends Controller
{
    /* **************************************** Public **************************************** */
    /**
     * @param Printer  $printer
     * @param PrintJob $printJob
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function complete(Printer $printer, PrintJob $printJob) : JsonResponse
    {
        abort_if($printJob->printer_id !== $printer->id, 404);

        PrinterService::completePrintJob($printJob);

        return response()->json([
            'success' => true,
            'message' => __('printer.printing_tasks_completed'),
        ]);
    }

    public function create(Printer $printer)
    {
        $printJob = null;

        return view('printers.print-job-form', compact('printJob', 'printer'));
    }

    public function destroy(Printer $printer, PrintJob $printJob) : JsonResponse
    {
        abort_if($printJob->printer_id !== $printer->id, 404);

        $printJob->delete();

        return response()->json(['success' => true]);
    }

    /**
     * @throws Throwable
     */
    public function store(PrintJobRequest $request, Printer $printer) : JsonResponse
    {
        $printJob = PrinterService::getActivePrintJob($printer, 'undefined');
        if ($printJob) {
            return response()->json([
                'success' => false,
                'message' => __('printer.print_job_already_exists'),
            ]);
        }

        $result = PrinterService::createPrintJob($printer, $request->filename);

        return response()->json(['success' => $result['success']]);
    }

}
