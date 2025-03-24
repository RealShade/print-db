<?php

namespace App\Http\Controllers;

use App\Traits\ParsesFilenameTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToolsController extends Controller
{
    use ParsesFilenameTemplate;

    public function index(): View
    {
        return view('tools.index');
    }

    public function validateFilename(Request $request): JsonResponse
    {
        $validationResult = $this->parseFilename($request->filename, $request->user()->id);

        return response()->json($validationResult);
    }
}
