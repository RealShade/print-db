<?php
// app/Http/Controllers/Print/FilamentTypeController.php

namespace App\Http\Controllers\Filament;

use App\Http\Controllers\Controller;
use App\Http\Requests\Filament\FilamentTypeRequest;
use App\Models\FilamentType;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FilamentTypeController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create() : View
    {
        $filamentType = null;

        return view('filament.types.form', compact('filamentType'));
    }

    public function destroy(FilamentType $filamentType)
    {
        $filamentType->delete();

        return redirect(route('print.filament-types.index'));
    }

    public function edit(FilamentType $filamentType) : View
    {
        return view('filament.types.form', compact('filamentType'));
    }

    public function index() : View
    {
        $filamentTypes = FilamentType::where('user_id', auth()->id())
            ->orderBy('name', 'asc')
            ->paginate();

        return view('filament.types.index', compact('filamentTypes'));
    }

    public function store(FilamentTypeRequest $request) : JsonResponse
    {
        $filamentType          = new FilamentType($request->validated());
        $filamentType->user_id = auth()->id();
        $filamentType->save();

        return response()->json(['success' => true]);
    }

    public function update(FilamentTypeRequest $request, FilamentType $filamentType) : JsonResponse
    {
        $filamentType->update($request->validated());

        return response()->json(['success' => true]);
    }
}
