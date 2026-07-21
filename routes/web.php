<?php

use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', DashboardController::class)
        ->middleware('role:admin')
        ->name('dashboard.admin');

    Route::get('/technician/dashboard', DashboardController::class)
        ->middleware('role:technician')
        ->name('dashboard.technician');

    Route::get('/requester/dashboard', DashboardController::class)
        ->middleware('role:requester')
        ->name('dashboard.requester');

    Route::resource('tickets', TicketController::class);

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::resource('ticket-categories', TicketCategoryController::class)
                ->except('show');
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
