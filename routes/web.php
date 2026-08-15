<?php

use App\Http\Controllers\BuilderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/demo', [BuilderController::class, 'demo'])->name('builder.demo');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/builder/{project_slug}', [BuilderController::class, 'workspace'])
    ->middleware(['auth'])
    ->name('builder.workspace');

Route::post('/builder/{project_slug}/save', [BuilderController::class, 'save'])
    ->middleware(['auth'])
    ->name('builder.save');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/projects', [DashboardController::class, 'store'])->name('projects.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/projects/{id}', [DashboardController::class, 'destroy'])->name('projects.destroy');
});

require __DIR__.'/auth.php';

