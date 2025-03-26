<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApiTokenController extends Controller
{

    /* **************************************** Public **************************************** */
    public function destroy(ApiToken $token) : JsonResponse
    {
        $token->delete();

        return response()->json(['success' => true]);
    }

    public function index() : View
    {
        $tokens = auth()->user()->apiTokens()->latest()->get();

        return view('settings.api-tokens.index', compact('tokens'));
    }

    public function store(): JsonResponse
    {
        auth()->user()->apiTokens()->create([
            'token' => Str::random(64)
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}
