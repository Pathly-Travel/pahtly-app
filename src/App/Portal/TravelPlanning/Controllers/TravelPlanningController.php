<?php

namespace Src\App\Portal\TravelPlanning\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Domain\TravelPlanning\Actions\GenerateTravelPlanAction;
use Src\Domain\TravelPlanning\Data\TravelPlanningRequestData;
use Src\Support\Controllers\Controller;

class TravelPlanningController extends Controller
{
    public function __construct(
        private GenerateTravelPlanAction $generateTravelPlanAction
    ) {}

    public function generatePlan(TravelPlanningRequestData $requestData): JsonResponse
    {
        try {
            $result = ($this->generateTravelPlanAction)($requestData);
            
            return response()->json([
                'success' => true,
                'data' => $result->toArray(),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 