<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\PartRequest;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PartController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create() : View
    {
        $part = null;

        return view('print.parts.form', compact('part'));
    }

    public function edit(Part $part) : View
    {
        return view('print.parts.form', compact('part'));
    }

    public function index() : View
    {
        $parts = Part::where('user_id', auth()->id())
            ->latest()
            ->paginate();

        return view('print.parts.index', compact('parts'));
    }

    public function store(PartRequest $request) : JsonResponse
    {
        $part          = new Part($request->validated());
        $part->user_id = auth()->id();
        $part->save();

        return response()->json(['success' => true]);
    }

    public function update(PartRequest $request, Part $part) : JsonResponse
    {
        $part->update($request->validated());

        return response()->json(['success' => true]);
    }

    public function destroy(Part $part) : JsonResponse
    {
        $part->delete();

        return response()->json(['success' => true]);
    }
}
