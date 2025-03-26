<?php

namespace App\Http\Controllers;

use App\Enums\PrinterStatus;
use App\Http\Requests\PrinterRequest;
use App\Models\Printer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrinterController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create() : View
    {
        $printer = null;

        return view('printers.form', compact('printer'));
    }

    public function destroy(Printer $printer) : RedirectResponse
    {
        $printer->delete();

        return redirect()
            ->route('printers.index')
            ->with('success', __('printer.status.deleted'));
    }

    public function edit(Printer $printer) : View
    {
        return view('printers.form', compact('printer'));
    }

    public function index() : View
    {
        $printers = auth()->user()->printers()->latest()->get();

        return view('printers.index', compact('printers'));
    }

    public function store(PrinterRequest $request) : JsonResponse
    {
        auth()->user()->printers()->create([
            'name'   => $request->name,
            'status' => PrinterStatus::ACTIVE,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(PrinterRequest $request, Printer $printer) : JsonResponse
    {
        $printer->update($request->validated());

        return response()->json(['success' => true]);
    }
}
