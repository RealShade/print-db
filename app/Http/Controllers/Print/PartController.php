<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\Part;

class PartController extends Controller
{

/* **************************************** Public **************************************** */
    public function index()
    {
        $parts = Part::where('user_id', auth()->id())
            ->latest()
            ->paginate();

        return view('print.parts.index', compact('parts'));
    }

    public function store(PartRequest $request)
    {
        $part          = new Part($request->validated());
        $part->user_id = auth()->id();
        $part->save();

        return response()->json(['success' => true]);
    }
}
