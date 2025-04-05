<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilamentLoadedRequest;
use App\Models\FilamentLoaded;
use App\Models\Printer;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FilamentLoadedController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create(Printer $printer) : View
    {
        $filamentLoaded = null;
        $filamentSpools = auth()->user()->filamentSpools()->with('filament')->get();

        return view('printers.filament-loaded-form', compact('filamentLoaded', 'printer', 'filamentSpools'));
    }

    public function destroy(Printer $printer, FilamentLoaded $filamentLoaded) : JsonResponse
    {
        abort_if($filamentLoaded->printer_id !== $printer->id, 403);

        $filamentLoaded->delete();

        return response()->json(['success' => true]);
    }

    public function edit(Printer $printer, FilamentLoaded $filamentLoaded) : View
    {
        abort_if($filamentLoaded->printer_id !== $printer->id, 403);

        $filamentSpools = auth()->user()->filamentSpools()->with('filament')->get();

        return view('printers.filament-loaded-form', compact('filamentLoaded', 'printer', 'filamentSpools'));
    }

    public function store(Printer $printer, FilamentLoadedRequest $request) : JsonResponse
    {
        $data               = $request->validated();
        $data['printer_id'] = $printer->id;

        FilamentLoaded::create($data);

        return response()->json(['success' => true]);
    }

    public function update(FilamentLoadedRequest $request, Printer $printer, FilamentLoaded $filamentLoaded) : JsonResponse
    {
        abort_if($filamentLoaded->printer_id !== $printer->id, 403);

        $filamentLoaded->update($request->validated());

        return response()->json(['success' => true]);
    }
}
