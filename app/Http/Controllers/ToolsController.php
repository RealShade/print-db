<?php

namespace App\Http\Controllers;

use App\Traits\ParseFilename;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToolsController extends Controller
{
    use ParseFilename;

    public function index(): View
    {
        return view('tools.index');
    }

    public function validateFilename(Request $request): JsonResponse
    {
        $validationResult = $this->parseFilename($request->filename);

        return response()->json($validationResult);
    }
}
