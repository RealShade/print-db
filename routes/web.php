<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Print\PartController;
use App\Http\Controllers\Print\TaskController;
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

Route::middleware(['auth', 'check.user.status'])->group(function() {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('print')->name('print.')->group(function() {
        Route::resource('parts', PartController::class)->except(['show', 'destroy']);
        Route::resource('tasks', TaskController::class)->except(['show', 'destroy']);
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function() {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
