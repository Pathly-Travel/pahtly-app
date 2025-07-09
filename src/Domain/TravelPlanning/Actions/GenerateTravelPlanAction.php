<?php

namespace Src\Domain\TravelPlanning\Actions;

use Illuminate\Support\Facades\Log;
use Src\Domain\TravelPlanning\Data\TravelPlanningRequestData;
use Src\Support\Connectors\OpenAi\Requests\TravelPlanningRequest;
use Src\Support\Connectors\OpenAi\OpenAiConnector;

class GenerateTravelPlanAction
{
    public function __construct(
        private OpenAiConnector $connector
    ) {}

    public function __invoke(TravelPlanningRequestData $requestData)
    {
        try {
            $request = new TravelPlanningRequest($requestData);
            $response = $this->connector->send($request);
            
            $responseData = $request->createDtoFromResponse($response);
        
            return $responseData;
            
        } catch (\Exception $e) {
            Log::error('Travel plan generation failed', [
                'error' => $e->getMessage(),
                'request_data' => $requestData->toArray()
            ]);
            
            throw $e;
        }
    }
} 