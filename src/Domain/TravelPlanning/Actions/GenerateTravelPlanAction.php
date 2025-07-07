<?php

namespace Src\Domain\TravelPlanning\Actions;

use Illuminate\Support\Facades\Log;
use Src\Domain\TravelPlanning\Data\OpenAIResponseData;
use Src\Domain\TravelPlanning\Data\TravelPlanningRequestData;
use Src\Support\Connectors\OpenAi\Requests\ChatCompletionRequest;
use Src\Support\Connectors\OpenAi\OpenAiConnector;

class GenerateTravelPlanAction
{
    public function __construct(
        private OpenAiConnector $connector
    ) {}

    public function __invoke(TravelPlanningRequestData $requestData): OpenAIResponseData
    {
        try {
            $request = new ChatCompletionRequest($requestData);
            $response = $this->connector->send($request);
            
            $responseData = $response->json();
            
            // Extract the content from the OpenAI response
            $content = $responseData['choices'][0]['message']['content'] ?? '';
            
            // Parse the JSON response from OpenAI
            $parsedContent = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse OpenAI response JSON', [
                    'content' => $content,
                    'error' => json_last_error_msg()
                ]);
                
                throw new \Exception('Invalid JSON response from OpenAI');
            }
            
            return new OpenAIResponseData(
                cities: $parsedContent['cities'] ?? [],
                rawResponse: $content,
                usage: $responseData['usage'] ?? null,
                model: $responseData['model'] ?? null
            );
            
        } catch (\Exception $e) {
            Log::error('Travel plan generation failed', [
                'error' => $e->getMessage(),
                'request_data' => $requestData->toArray()
            ]);
            
            throw $e;
        }
    }
} 