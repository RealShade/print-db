<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.api_token'])->group(function() {
    Route::match(['get', 'post'], '/', [TaskController::class, 'index']);
    Route::match(['get', 'post'], 'print-start', [TaskController::class, 'beforePrint']);
    Route::match(['get', 'post'], 'print-end', [TaskController::class, 'afterPrint']);
    Route::match(['get', 'post'], 'print-stop', [TaskController::class, 'stopPrint']);
});
