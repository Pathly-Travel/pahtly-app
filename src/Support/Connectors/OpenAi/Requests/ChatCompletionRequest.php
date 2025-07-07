<?php

namespace Src\Support\Connectors\OpenAi\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Src\Domain\TravelPlanning\Data\TravelPlanningRequestData;

class ChatCompletionRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private TravelPlanningRequestData $requestData,
        private string $model = 'gpt-4o-mini',
        private float $temperature = 0.7
    ) {}

    public function resolveEndpoint(): string
    {
        return 'chat/completions';
    }

    protected function defaultBody(): array
    {
        return [
            'model' => $this->model,
            'messages' => $this->buildMessages(),
            'temperature' => $this->temperature,
        ];
    }

    private function buildMessages(): array
    {
        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt = $this->buildUserPrompt();

        return [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            [
                'role' => 'user',
                'content' => $userPrompt,
            ],
        ];
    }

    private function buildSystemPrompt(): string
    {
        $preferredTravelTime = $this->requestData->maxTravelTimePerDay - 1;
        
        return "Je bent een ervaren travel agent gespecialiseerd in Interrail-reizen door Europa. Stel een realistische Interrail-route samen op basis van de volgende context. Gebruik alleen steden of dorpen die logisch zijn binnen het Europese spoorwegnet én passen binnen een {$this->requestData->transportType}. De route mag haltes overslaan als deze onpraktisch zijn (bijv. slechte verbinding of te lange reistijd).

Context:
- Interesses: {$this->requestData->getInterestsString()}
- **Harde limiet:** een enkele {$this->requestData->transportType} mag NOOIT langer dan {$this->requestData->maxTravelTimePerDay} uur netto duren (incl. overstappen). Als een route dit toch vereist, **verwerp die route en bereken een alternatief**.
- **Streefwaarde:** probeer ritten rond {$preferredTravelTime} uur of minder te houden.
- Als geen logische route < {$this->requestData->maxTravelTimePerDay} uur beschikbaar is, **sla die halte dan over** en ga naar de volgende logische stad.
- Startlocatie: {$this->requestData->startLocation}.
- Reisdata: {$this->requestData->getFormattedStartDate()} – {$this->requestData->getFormattedEndDate()}.
- Aantal reizigers: {$this->requestData->numberOfTravelers} volwassenen.
- Vermijd bestemmingen waar de reistijd of overstappen buitenproportioneel zijn voor de gestelde limiet.
- Geef comfort voorrang en beperk onnodig lange reisdagen.
- **Controleer je eigen voorstel vóór je antwoordt.** Als één rit > {$this->requestData->maxTravelTimePerDay} uur is, herbereken totdat alle ritten ≤ {$this->requestData->maxTravelTimePerDay} uur zijn. Toon pas daarna de JSON-uitvoer.

Output:
Geef uitsluitend een geldige JSON-array met de te bezoeken steden in dit exacte formaat, zonder extra tekst of uitleg:
{
  \"cities\": [
    \"City1\",
    \"City2\",
    \"City3\"
  ]
}";
    }

    private function buildUserPrompt(): string
    {
        return "Genereer nu de stedenlijst. Gebruik daarbij verplicht deze haltes, in volgorde van voorkeur: {$this->requestData->getPreferredLocationsString()}. Vul de rest van de route logisch aan en geef alleen het JSON-resultaat.";
    }
} 