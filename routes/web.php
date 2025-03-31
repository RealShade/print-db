<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Print\FilamentTypeController;
use App\Http\Controllers\Print\FilamentVendorController;
use App\Http\Controllers\Print\PartController;
use App\Http\Controllers\Print\TaskController;
use App\Http\Controllers\Print\PartTaskController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\PrintingTaskController;
use App\Http\Controllers\Settings\ApiTokenController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\ToolsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function() {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    //    Route::get('register/success', function() {
    //        return view('auth.register-success');
    //    })->name('register.success');
});

Route::middleware(['auth', 'check.user.status', 'check.owner'])->group(function() {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('settings')->name('settings.')->group(function() {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::prefix('api-tokens')->name('api-tokens.')->group(function() {
            Route::get('/', [ApiTokenController::class, 'index'])->name('index');
            Route::post('/', [ApiTokenController::class, 'store'])->name('store');
            Route::delete('/{token}', [ApiTokenController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('print')->name('print.')->group(function() {
        // Маршруты для частей (parts)
        Route::get('parts', [PartController::class, 'index'])->name('parts.index');
        Route::get('parts/create', [PartController::class, 'create'])->name('parts.create');
        Route::post('parts', [PartController::class, 'store'])->name('parts.store');
        Route::get('parts/{part}/edit', [PartController::class, 'edit'])->name('parts.edit');
        Route::put('parts/{part}', [PartController::class, 'update'])->name('parts.update');
        Route::delete('parts/{part}', [PartController::class, 'destroy'])->name('parts.destroy');

        // Маршруты для задач (tasks)
        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

        // Маршруты для частей задач (task-parts)
        Route::get('task-parts/{task}/create', [PartTaskController::class, 'create'])
            ->name('task-parts.create');
        Route::post('task-parts/{task}', [PartTaskController::class, 'store'])
            ->name('task-parts.store');
        Route::get('task-parts/{partTask}/edit', [PartTaskController::class, 'edit'])
            ->name('task-parts.edit');
        Route::put('task-parts/{partTask}', [PartTaskController::class, 'update'])
            ->name('task-parts.update');
        Route::delete('task-parts/{partTask}', [PartTaskController::class, 'destroy'])
            ->name('task-parts.destroy');
        Route::post('task-parts/{partTask}/add-printed', [PartTaskController::class, 'addPrinted'])
            ->name('task-parts.add-printed');

        // Маршруты для вендоров филамента (filament-vendors)
        Route::get('filament-vendors', [FilamentVendorController::class, 'index'])->name('filament-vendors.index');
        Route::get('filament-vendors/create', [FilamentVendorController::class, 'create'])->name('filament-vendors.create');
        Route::post('filament-vendors', [FilamentVendorController::class, 'store'])->name('filament-vendors.store');
        Route::get('filament-vendors/{vendor}/edit', [FilamentVendorController::class, 'edit'])->name('filament-vendors.edit');
        Route::put('filament-vendors/{vendor}', [FilamentVendorController::class, 'update'])->name('filament-vendors.update');
        Route::delete('filament-vendors/{vendor}', [FilamentVendorController::class, 'destroy'])->name('filament-vendors.destroy');

        // Маршруты для типов филамента (filament-types)
        Route::get('filament-types', [FilamentTypeController::class, 'index'])->name('filament-types.index');
        Route::get('filament-types/create', [FilamentTypeController::class, 'create'])->name('filament-types.create');
        Route::post('filament-types', [FilamentTypeController::class, 'store'])->name('filament-types.store');
        Route::get('filament-types/{filament_type}/edit', [FilamentTypeController::class, 'edit'])->name('filament-types.edit');
        Route::put('filament-types/{filament_type}', [FilamentTypeController::class, 'update'])->name('filament-types.update');
        Route::delete('filament-types/{filament_type}', [FilamentTypeController::class, 'destroy'])->name('filament-types.destroy');
    });

    Route::resource('printers', PrinterController::class)->except(['show']);
    Route::post('printers/{printer}/toggle-status', [PrinterController::class, 'toggleStatus'])
        ->name('printers.toggle-status');

    Route::get('printing-tasks/{printer}/create', [PrintingTaskController::class, 'create'])->name('printing-tasks.create');
    Route::post('printing-tasks/{printer}', [PrintingTaskController::class, 'store'])->name('printing-tasks.store');
    Route::get('printing-tasks/{printingTask}', [PrintingTaskController::class, 'edit'])->name('printing-tasks.edit');
    Route::put('printing-tasks/{printingTask}', [PrintingTaskController::class, 'update'])->name('printing-tasks.update');
    Route::delete('printing-tasks/{printingTask}', [PrintingTaskController::class, 'destroy'])->name('printing-tasks.destroy');
    Route::get('printing-tasks/{task}/parts', [PrintingTaskController::class, 'getParts'])
        ->name('printing-tasks.parts');

    Route::get('tools', [ToolsController::class, 'index'])->name('tools.index');
    Route::post('tools/validate-filename', [ToolsController::class, 'validateFilename'])
        ->name('tools.validate-filename');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function() {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

