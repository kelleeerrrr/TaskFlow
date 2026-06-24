<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RequestController;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::aliasMiddleware('role', EnsureUserHasRole::class);
Route::aliasMiddleware('active.account', EnsureAccountIsActive::class);

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'active.account'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    });

    Route::middleware('role:super_admin|manager')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::post('/detect-late', [ReportsController::class, 'detectLate'])->name('detect-late');
            Route::get('/productivity', [ReportsController::class, 'productivity'])->name('productivity');
            Route::get('/per-user', [ReportsController::class, 'perUserReport'])->name('per-user');
            Route::get('/user-registrations', [ReportsController::class, 'userRegistrations'])->name('user-registrations');
        });

        Route::get('/collaborations', [TaskController::class, 'collaborationRequests'])->name('collaborations.index');
    });

    Route::middleware('role:manager')->group(function () {
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [RequestController::class, 'index'])->name('index');
            Route::post('/collaborations/{task}/approve', [RequestController::class, 'approveCollaboration'])->name('approve-collaboration');
            Route::post('/collaborations/{task}/reject', [RequestController::class, 'rejectCollaboration'])->name('reject-collaboration');
            Route::post('/time-revisions/{id}/approve', [RequestController::class, 'approveTimeRevision'])->name('approve-time-revision');
            Route::post('/time-revisions/{id}/reject', [RequestController::class, 'rejectTimeRevision'])->name('reject-time-revision');
            Route::get('/collaboration/{id}/details', [RequestController::class, 'getCollaborationDetails'])->name('collaboration-details');
            Route::get('/time-revision/{id}/details', [RequestController::class, 'getTimeRevisionDetails'])->name('time-revision-details');
        });
    });

    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
    Route::post('/tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::post('/tasks/{task}/invite', [TaskController::class, 'inviteCollaborator'])->name('tasks.invite');
    Route::post('/tasks/{task}/accept-invitation', [TaskController::class, 'acceptInvitation'])->name('tasks.accept-invitation');
    Route::post('/tasks/{task}/reject-invitation', [TaskController::class, 'rejectInvitation'])->name('tasks.reject-invitation');
    Route::post('/tasks/{task}/time-revision', [TaskController::class, 'requestTimeRevision'])->name('tasks.time-revision');
    Route::get('/tasks/{task}/details', [TaskController::class, 'getTaskDetails'])->name('tasks.details');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
