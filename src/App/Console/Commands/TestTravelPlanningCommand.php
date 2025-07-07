<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Src\Domain\TravelPlanning\Actions\GenerateTravelPlanAction;
use Src\Domain\TravelPlanning\Data\TravelPlanningRequestData;   

class TestTravelPlanningCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:travel-planning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the travel planning OpenAI integration';

    /**
     * Execute the console command.
     */
    public function handle(GenerateTravelPlanAction $action): int
    {
        $this->info('Testing Travel Planning OpenAI Integration...');
        
        // Create test data
        $requestData = new TravelPlanningRequestData(
            startDate: '2025-05-10',
            endDate: '2025-05-20',
            preferredLocations: ['Parijs', 'Zürich', 'Milaan'],
            interests: [
                'Europese cultuur ervaren',
                'lokale keukens proeven',
                'indrukwekkende landschappen zien',
                'historische bezienswaardigheden bezoeken',
                'wandelen en andere buitenactiviteiten',
                'lokale geschiedenis leren'
            ],
            startLocation: 'Amsterdam Centraal',
            numberOfTravelers: 4,
            maxTravelTimePerDay: 6,
            transportType: 'Interrail-Global Pass'
        );

        try {
            $this->info('Sending request to OpenAI...');
            $result = $action($requestData);
            
            $this->info('✅ Success! Generated travel plan:');
            $this->newLine();
            
            $this->info('📍 Cities to visit:');
            foreach ($result->cities as $index => $city) {
                $this->line('  ' . ($index + 1) . '. ' . $city);
            }
            
            $this->newLine();
            $this->info('📊 API Usage:');
            if ($result->usage) {
                $this->line('  Model: ' . $result->model);
                $this->line('  Prompt tokens: ' . $result->usage['prompt_tokens']);
                $this->line('  Completion tokens: ' . $result->usage['completion_tokens']);
                $this->line('  Total tokens: ' . $result->usage['total_tokens']);
            }
            
            $this->newLine();
            $this->info('🔍 Raw response (first 200 chars):');
            $this->line('  ' . substr($result->rawResponse, 0, 200) . '...');
            
            return self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->newLine();
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            
            return self::FAILURE;
        }
    }
} 