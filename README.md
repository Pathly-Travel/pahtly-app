# Laravel DTO - Domain Driven Design

Dit project demonstreert het gebruik van het Spatie DTO design pattern in combinatie met Domain Driven Design (DDD) in Laravel.

## Architectuur

Het project is georganiseerd in verschillende domeinen, elk met hun eigen verantwoordelijkheden:

### Domain Structure

```
src/
├── App/
│   ├── Foundation/
│   │   └── Application.php
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/
│   │       └── Settings/
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── DomainServiceProvider.php
├── Domain/
│   ├── Auth/
│   │   ├── Actions/
│   │   │   ├── LoginAction.php
│   │   │   └── RegisterAction.php
│   │   └── Data/
│   │       ├── LoginData.php
│   │       └── RegisterData.php
│   ├── Settings/
│   │   ├── Actions/
│   │   │   └── UpdatePasswordAction.php
│   │   └── Data/
│   │       ├── PasswordUpdateData.php
│   │       └── ProfileUpdateData.php
│   └── User/
│       ├── Actions/
│       │   ├── DeleteUserAction.php
│       │   └── UpdateUserProfileAction.php
│       ├── Data/
│       │   └── UserData.php
│       └── Models/
│           └── User.php
└── Support/
    ├── Helpers/
    │   └── DomainHelper.php
    └── Traits/
        └── HasDomainData.php
```

## Spatie Laravel Data

We gebruiken [Spatie Laravel Data](https://spatie.be/docs/laravel-data) voor het implementeren van DTOs (Data Transfer Objects). Dit biedt:

- **Type Safety**: Automatische type validatie
- **Validation**: Ingebouwde validatie met attributes
- **Transformation**: Eenvoudige conversie tussen arrays, requests en objecten
- **IDE Support**: Volledige autocompletion en type hints

### Voorbeeld DTO

```php
<?php

namespace Src\Domain\User\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;

class UserData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $name,
        
        #[Required, Email, Max(255)]
        public string $email,
        
        public ?string $emailVerifiedAt = null,
        
        public ?int $id = null,
    ) {}
}
```

## Domain Actions

Elke domain heeft Actions die de business logic bevatten:

```php
<?php

namespace Src\Domain\User\Actions;

use Src\Domain\User\Data\UserData;
use Src\Domain\User\Models\User;

class UpdateUserProfileAction
{
    public function __invoke(User $user, UserData $userData): User
    {
        $user->fill($userData->except('id', 'emailVerifiedAt')->toArray());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}
```

## Controller Usage

Controllers gebruiken DTOs en Actions:

```php
public function update(Request $request): RedirectResponse
{
    $profileData = ProfileUpdateData::from($request->all());
    
    app(UpdateUserProfileAction::class)(auth()->user(), $profileData);

    return to_route('profile.edit');
}
```

## Nieuwe Structuur Voordelen

De nieuwe `src/` structuur biedt:

1. **App Layer**: HTTP controllers, providers en foundation klassen
2. **Domain Layer**: Business logic, actions en data objecten  
3. **Support Layer**: Herbruikbare helpers en traits

Deze structuur volgt de conflictbemiddeling-nl architectuur en biedt betere scheiding van verantwoordelijkheden.

## Voordelen

1. **Separation of Concerns**: Elke domain heeft zijn eigen verantwoordelijkheden
2. **Type Safety**: DTOs zorgen voor type veiligheid
3. **Validation**: Automatische validatie via DTO attributes
4. **Testability**: Actions zijn eenvoudig te testen
5. **Maintainability**: Code is beter georganiseerd en onderhoudbaar
6. **Reusability**: Actions en DTOs kunnen hergebruikt worden

## Installation

```bash
composer install
npm install
```

## Usage

```bash
# Development
composer dev

# Testing
composer test
```

## Dependencies

- **Laravel 12**: Het framework
- **Spatie Laravel Data**: Voor DTOs
- **Inertia.js**: Voor frontend integratie

## Inspiratie

Dit project is geïnspireerd door de architectuur van conflictbemiddeling-nl en volgt de best practices voor Domain Driven Design in Laravel applicaties. 