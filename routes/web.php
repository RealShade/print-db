<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrinterFilamentSlotController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Filament\FilamentController;
use App\Http\Controllers\Filament\FilamentTypeController;
use App\Http\Controllers\Filament\FilamentPackagingTypeController;
use App\Http\Controllers\Filament\FilamentVendorController;
use App\Http\Controllers\Filament\FilamentSpoolController;
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
    });

    Route::prefix('filament')->name('filament.')->group(function() {
        // Маршруты для вендоров филамента (filament-vendors)
        Route::get('vendors', [FilamentVendorController::class, 'index'])->name('vendors.index');
        Route::get('vendors/create', [FilamentVendorController::class, 'create'])->name('vendors.create');
        Route::post('vendors', [FilamentVendorController::class, 'store'])->name('vendors.store');
        Route::get('vendors/{vendor}/edit', [FilamentVendorController::class, 'edit'])->name('vendors.edit');
        Route::put('vendors/{vendor}', [FilamentVendorController::class, 'update'])->name('vendors.update');
        Route::delete('vendors/{vendor}', [FilamentVendorController::class, 'destroy'])->name('vendors.destroy');

        // Маршруты для типов филамента (filament-types)
        Route::get('types', [FilamentTypeController::class, 'index'])->name('types.index');
        Route::get('types/create', [FilamentTypeController::class, 'create'])->name('types.create');
        Route::post('types', [FilamentTypeController::class, 'store'])->name('types.store');
        Route::get('types/{filament_type}/edit', [FilamentTypeController::class, 'edit'])->name('types.edit');
        Route::put('types/{filament_type}', [FilamentTypeController::class, 'update'])->name('types.update');
        Route::delete('types/{filament_type}', [FilamentTypeController::class, 'destroy'])->name('types.destroy');

        // Маршруты для типов фасовки филамента (packaging-types)
        Route::get('packaging', [FilamentPackagingTypeController::class, 'index'])->name('packaging.index');
        Route::get('packaging/create', [FilamentPackagingTypeController::class, 'create'])->name('packaging.create');
        Route::post('packaging', [FilamentPackagingTypeController::class, 'store'])->name('packaging.store');
        Route::get('packaging/{packaging_type}/edit', [FilamentPackagingTypeController::class, 'edit'])->name('packaging.edit');
        Route::put('packaging/{packaging_type}', [FilamentPackagingTypeController::class, 'update'])->name('packaging.update');
        Route::delete('packaging/{packaging_type}', [FilamentPackagingTypeController::class, 'destroy'])->name('packaging.destroy');

        // Маршруты для филаментов (filament)
        Route::get('filament', [FilamentController::class, 'index'])->name('filament.index');
        Route::get('filament/create', [FilamentController::class, 'create'])->name('filament.create');
        Route::post('filament', [FilamentController::class, 'store'])->name('filament.store');
        Route::get('filament/{filament}/edit', [FilamentController::class, 'edit'])->name('filament.edit');
        Route::put('filament/{filament}', [FilamentController::class, 'update'])->name('filament.update');
        Route::delete('filament/{filament}', [FilamentController::class, 'destroy'])->name('filament.destroy');

        // Маршруты для катушек филамента (filament-spools)
        Route::get('spools', [FilamentSpoolController::class, 'index'])->name('spools.index');
        Route::get('spools/create', [FilamentSpoolController::class, 'create'])->name('spools.create');
        Route::post('spools', [FilamentSpoolController::class, 'store'])->name('spools.store');
        Route::get('spools/{spool}/edit', [FilamentSpoolController::class, 'edit'])->name('spools.edit');
        Route::put('spools/{spool}', [FilamentSpoolController::class, 'update'])->name('spools.update');
        Route::delete('spools/{spool}', [FilamentSpoolController::class, 'destroy'])->name('spools.destroy');
    });

    Route::resource('printers', PrinterController::class)->except(['show']);
    Route::post('/printers/{printer}/complete-print', [PrinterController::class, 'complete'])
        ->name('printers.complete-print');

    Route::get('printers/{printer}/filament-slot/create', [PrinterFilamentSlotController::class, 'create'])
        ->name('filament-slot.create');
    Route::get('printers/{printer}/filament-slot/{filamentSlot}/edit', [PrinterFilamentSlotController::class, 'edit'])
        ->name('filament-slot.edit');
    Route::post('printers/{printer}/filament-slot', [PrinterFilamentSlotController::class, 'store'])
        ->name('filament-slot.store');
    Route::put('printers/{printer}/filament-slot/{filamentSlot}', [PrinterFilamentSlotController::class, 'update'])
        ->name('filament-slot.update');
    Route::delete('printers/{printer}/filament-slot/{filamentSlot}', [PrinterFilamentSlotController::class, 'destroy'])
        ->name('filament-slot.destroy');


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

