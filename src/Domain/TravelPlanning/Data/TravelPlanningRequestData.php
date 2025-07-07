<?php

namespace Src\Domain\TravelPlanning\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class TravelPlanningRequestData extends Data
{
    public function __construct(
        #[Required]
        public string $startDate,

        #[Required]
        public string $endDate,

        #[Required]
        public array $preferredLocations,

        #[Required]
        public array $interests,

        public string $startLocation = 'Amsterdam Centraal',

        public int $numberOfTravelers = 4,

        public int $maxTravelTimePerDay = 6,

        public string $transportType = 'Interrail-Global Pass',
    ) {}

    public function getFormattedStartDate(): string
    {
        return Carbon::parse($this->startDate)->format('j F Y');
    }

    public function getFormattedEndDate(): string
    {
        return Carbon::parse($this->endDate)->format('j F Y');
    }

    public function getPreferredLocationsString(): string
    {
        return implode(', ', $this->preferredLocations);
    }

    public function getInterestsString(): string
    {
        return implode(', ', $this->interests);
    }
} 