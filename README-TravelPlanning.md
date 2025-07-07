# Travel Planning Domain - OpenAI Integration

Deze module integreert OpenAI's Chat Completion API met Laravel Saloon om intelligente reisplannen te genereren voor Interrail-reizen door Europa.

## Overzicht

De TravelPlanning domain bevat:
- **OpenAI Connector**: Saloon-gebaseerde connector voor OpenAI API
- **Data Transfer Objects**: Type-safe data objecten voor requests en responses
- **Actions**: Business logic voor het genereren van reisplannen
- **Controller**: HTTP endpoint voor API requests

## Installatie

1. Voeg je OpenAI API key toe aan je `.env` bestand:
```env
OPENAI_API_KEY=your-openai-api-key-here
OPENAI_ORGANIZATION=your-openai-organization-id-here
```

2. De benodigde dependencies zijn al geïnstalleerd:
   - `saloonphp/saloon` - Voor API requests
   - `spatie/laravel-data` - Voor data transfer objects

## Gebruik

### Via HTTP API

POST naar `/travel-planning/generate` met de volgende JSON body:

```json
{
  "startDate": "2025-05-10",
  "endDate": "2025-05-20",
  "preferredLocations": ["Parijs", "Zürich", "Milaan"],
  "interests": [
    "Europese cultuur ervaren",
    "lokale keukens proeven",
    "indrukwekkende landschappen zien",
    "historische bezienswaardigheden bezoeken",
    "wandelen en andere buitenactiviteiten",
    "lokale geschiedenis leren"
  ],
  "startLocation": "Amsterdam Centraal",
  "numberOfTravelers": 4,
  "maxTravelTimePerDay": 6,
  "transportType": "Interrail-Global Pass"
}
```

### Via PHP Code

```php
use Src\Domain\TravelPlanning\Actions\GenerateTravelPlanAction;
use Src\Domain\TravelPlanning\Data\TravelPlanningRequestData;

$requestData = new TravelPlanningRequestData(
    startDate: '2025-05-10',
    endDate: '2025-05-20',
    preferredLocations: ['Parijs', 'Zürich', 'Milaan'],
    interests: ['Europese cultuur ervaren', 'lokale keukens proeven'],
    startLocation: 'Amsterdam Centraal',
    numberOfTravelers: 4,
    maxTravelTimePerDay: 6,
    transportType: 'Interrail-Global Pass'
);

$action = app(GenerateTravelPlanAction::class);
$result = $action($requestData);

// $result->cities bevat de gegenereerde stedenlijst
// $result->usage bevat API usage informatie
```

## Architectuur

### Domain Layer (`src/Domain/TravelPlanning/`)

- **Data/**: Data transfer objects
  - `TravelPlanningRequestData`: Request parameters
  - `OpenAIResponseData`: API response data
- **Actions/**: Business logic
  - `GenerateTravelPlanAction`: Hoofdlogica voor reisplan generatie
- **Connectors/**: API connectors
  - `OpenAIConnector`: Saloon connector voor OpenAI API
- **Requests/**: API request classes
  - `ChatCompletionRequest`: OpenAI chat completion request

### Application Layer (`src/App/Portal/TravelPlanning/`)

- **Controllers/**: HTTP controllers
  - `TravelPlanningController`: REST API endpoint

## Configureerbare Parameters

De volgende parameters kunnen worden geconfigureerd per request:

| Parameter | Type | Vereist | Beschrijving |
|-----------|------|---------|--------------|
| `startDate` | string | Ja | Startdatum van de reis (YYYY-MM-DD) |
| `endDate` | string | Ja | Einddatum van de reis (YYYY-MM-DD) |
| `preferredLocations` | array | Ja | Gewenste bestemmingen |
| `interests` | array | Ja | Interesses van de reiziger |
| `startLocation` | string | Nee | Startlocatie (default: Amsterdam Centraal) |
| `numberOfTravelers` | integer | Nee | Aantal reizigers (default: 4) |
| `maxTravelTimePerDay` | integer | Nee | Max reistijd per dag in uren (default: 6) |
| `transportType` | string | Nee | Transportmiddel (default: Interrail-Global Pass) |

## Response Format

De API retourneert een JSON response met het volgende format:

```json
{
  "success": true,
  "data": {
    "cities": [
      "Amsterdam",
      "Parijs",
      "Zürich",
      "Milaan",
      "Florence"
    ],
    "rawResponse": "JSON string zoals ontvangen van OpenAI",
    "usage": {
      "prompt_tokens": 425,
      "completion_tokens": 50,
      "total_tokens": 475
    },
    "model": "gpt-4o-mini"
  }
}
```

## Error Handling

- Alle API errors worden gelogd via Laravel's logging systeem
- Invalid JSON responses van OpenAI worden afgevangen
- HTTP errors worden afgehandeld met proper error responses
- Alle errors bevatten betekenisvolle error messages

## Testing

Zie `src/Domain/TravelPlanning/Examples/ExampleUsage.php` voor voorbeelden van het gebruik van de functionaliteit.

## Extensies

De module kan eenvoudig worden uitgebreid met:
- Andere AI providers (Claude, Gemini, etc.)
- Verschillende reistypes (auto, vliegtuig, etc.)
- Meer geavanceerde filtering opties
- Integratie met echte treinschema's 