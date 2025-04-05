<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrinterFilamentSlotRequest;
use App\Models\PrinterFilamentSlot;
use App\Models\Printer;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PrinterFilamentSlotController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create(Printer $printer) : View
    {
        $filamentSlot = null;
        $filamentSpools = auth()->user()->filamentSpools()->with('filament')->get();

        return view('printers.filament-slot-form', compact('filamentSlot', 'printer', 'filamentSpools'));
    }

    public function destroy(Printer $printer, PrinterFilamentSlot $filamentSlot) : JsonResponse
    {
        abort_if($filamentSlot->printer_id !== $printer->id, 403);

        $filamentSlot->delete();

        return response()->json(['success' => true]);
    }

    public function edit(Printer $printer, PrinterFilamentSlot $filamentSlot) : View
    {
        abort_if($filamentSlot->printer_id !== $printer->id, 403);

        $filamentSpools = auth()->user()->filamentSpools()->with('filament')->get();

        return view('printers.filament-slot-form', compact('filamentSlot', 'printer', 'filamentSpools'));
    }

    public function store(Printer $printer, PrinterFilamentSlotRequest $request) : JsonResponse
    {
        $data               = $request->validated();
        $data['printer_id'] = $printer->id;

        PrinterFilamentSlot::create($data);

        return response()->json(['success' => true]);
    }

    public function update(PrinterFilamentSlotRequest $request, Printer $printer, PrinterFilamentSlot $filamentSlot) : JsonResponse
    {
        abort_if($filamentSlot->printer_id !== $printer->id, 403);

        $filamentSlot->update($request->validated());

        return response()->json(['success' => true]);
    }
}
