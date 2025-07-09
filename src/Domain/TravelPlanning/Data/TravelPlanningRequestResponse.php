<?php

namespace Src\Domain\TravelPlanning\Data;

use Spatie\LaravelData\Data;

class TravelPlanningRequestResponse extends Data
{
    public function __construct(
        public array $cities,
    ) {}
} 