<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Src\App\Portal\TravelPlanning\Controllers\TravelPlanningController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/travel-planning', function () {
        return Inertia::render('TravelPlanner/TravelPlanning');
    })->name('travel-planning');
    
    Route::post('/travel-planning/generate', [TravelPlanningController::class, 'generatePlan'])
        ->name('travel-planning.generate');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
