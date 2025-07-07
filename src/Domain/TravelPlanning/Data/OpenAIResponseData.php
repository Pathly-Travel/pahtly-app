<?php

namespace Src\Domain\TravelPlanning\Data;

use Spatie\LaravelData\Data;

class OpenAIResponseData extends Data
{
    public function __construct(
        public array $cities,
        public ?string $rawResponse = null,
        public ?array $usage = null,
        public ?string $model = null,
    ) {}
} 