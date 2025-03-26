<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.api_token'])->group(function() {
    Route::get('tasks', [TaskController::class, 'index']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::match(['get', 'post'], 'print-start', [TaskController::class, 'beforePrint']);
    Route::match(['get', 'post'], 'print-end', [TaskController::class, 'afterPrint']);
});
