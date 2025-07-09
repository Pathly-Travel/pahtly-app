<?php

use Illuminate\Support\Facades\Route;
use Src\App\Portal\TravelPlanning\Controllers\TravelPlanningController;

// Route::middleware('auth:sanctum')->group(function () {
    Route::post('/travel-planning/generate', [TravelPlanningController::class, 'generatePlan'])
        ->name('api.travel-planning.generate');
// });
