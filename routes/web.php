<?php

use App\Http\Controllers\Admin\AssetCategoryController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketAssignmentController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketWorkflowController;
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

    Route::post('/tickets/{ticket}/assign', [TicketAssignmentController::class, 'store'])
        ->name('tickets.assign');
    Route::patch('/tickets/{ticket}/assign', [TicketAssignmentController::class, 'update'])
        ->name('tickets.reassign');
    Route::patch('/tickets/{ticket}/start-work', [TicketWorkflowController::class, 'startWork'])
        ->name('tickets.start-work');
    Route::patch('/tickets/{ticket}/cancel', [TicketWorkflowController::class, 'cancel'])
        ->name('tickets.cancel');
    Route::patch('/tickets/{ticket}/resolve', [TicketWorkflowController::class, 'resolve'])
        ->name('tickets.resolve');
    Route::patch('/tickets/{ticket}/close', [TicketWorkflowController::class, 'close'])
        ->name('tickets.close');
    Route::patch('/tickets/{ticket}/reopen', [TicketWorkflowController::class, 'reopen'])
        ->name('tickets.reopen');
    Route::post('/tickets/{ticket}/attachments', [TicketAttachmentController::class, 'store'])
        ->name('tickets.attachments.store');
    Route::get('/ticket-attachments/{attachment}/download', [TicketAttachmentController::class, 'download'])
        ->name('ticket-attachments.download');
    Route::scopeBindings()->group(function () {
        Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store'])
            ->name('tickets.comments.store');
        Route::patch('/tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'update'])
            ->name('tickets.comments.update');
        Route::delete('/tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'destroy'])
            ->name('tickets.comments.destroy');
    });
    Route::resource('tickets', TicketController::class);
    Route::resource('assets', AssetController::class);

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::resource('ticket-categories', TicketCategoryController::class)
                ->except('show');
            Route::resource('asset-categories', AssetCategoryController::class)
                ->except('show');
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
