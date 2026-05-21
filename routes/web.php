<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [PublicController::class, 'generate'])->name('home');
Route::get('/submissions/{token}', [PublicController::class, 'track'])
    ->name('submission.track')
    ->where('token', '[a-zA-Z0-9]{48}');

// Admin auth (no guard)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
});

// Admin protected
Route::prefix('admin')->name('admin.')->middleware(['auth', 'ensure.admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('submissions', [AdminSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/{submission}', [AdminSubmissionController::class, 'show'])->name('submissions.show');
    Route::post('submissions/{submission}/retry', [AdminSubmissionController::class, 'retry'])->name('submissions.retry');

    Route::get('templates', [AdminTemplateController::class, 'index'])->name('templates.index');
    Route::get('templates/create', [AdminTemplateController::class, 'create'])->name('templates.create');
    Route::post('templates', [AdminTemplateController::class, 'store'])->name('templates.store');
    Route::get('templates/{template}/edit', [AdminTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('templates/{template}', [AdminTemplateController::class, 'update'])->name('templates.update');
    Route::delete('templates/{template}', [AdminTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::patch('templates/{template}/toggle', [AdminTemplateController::class, 'toggle'])->name('templates.toggle');
});
