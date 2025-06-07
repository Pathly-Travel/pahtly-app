# Project Structure (Auto-generated)

**Generated:** 2025-06-07 12:33:02 UTC

## Domain Driven Design Architecture

This application follows Domain Driven Design (DDD) principles with a clean three-layer architecture:

```
src/
├── App/                          # Application Layer (HTTP/Infrastructure)
│   ├── Foundation/
│   │   └── Application.php       # Custom Laravel Application
│   ├── Portal/                   # HTTP Controllers grouped by domain
│   │   ├── Auth/
│   │   │   └── AuthenticatedSessionController.php
│   │   │   └── ConfirmablePasswordController.php
│   │   │   └── VerifyEmailController.php
│   │   ├── Settings/
│   │   │   └── ProfileController.php
│   │   │   └── PasswordController.php
│   ├── Middleware/               # HTTP Middleware
│   │   └── HandleInertiaRequests.php
│   │   └── HandleAppearance.php
│   └── Providers/
│       └── DomainServiceProvider.php
│       └── AppServiceProvider.php
├── Domain/                       # Domain Layer (Business Logic)
│   ├── User/                     # User Domain
│   │   ├── Actions/
│   │   │   ├── DeleteUserAction.php
│   │   │   ├── UpdateUserProfileAction.php
│   │   ├── Data/
│   │   │   ├── UserData.php
│   │   └── Models/
│   │       └── User.php
│   ├── Auth/                     # Auth Domain
│   │   ├── Actions/
│   │   │   ├── LoginAction.php
│   │   │   ├── RegisterAction.php
│   │   ├── Data/
│   │   │   ├── LoginData.php
│   │   │   ├── RegisterData.php
│   ├── Settings/                     # Settings Domain
│   │   ├── Actions/
│   │   │   ├── UpdatePasswordAction.php
│   │   ├── Data/
│   │   │   ├── PasswordUpdateData.php
│   │   │   ├── ProfileUpdateData.php
└── Support/                      # Support Layer (Shared Infrastructure)
    ├── Controllers/
    │   └── Controller.php
    ├── Helpers/
    │   └── DomainHelper.php
    ├── Traits/
    │   └── HasDomainData.php
```

## Layer Responsibilities

### Application Layer (`src/App/`)
- **Controllers**: Handle HTTP requests and coordinate with domain actions
- **Middleware**: Cross-cutting concerns (authentication, validation, etc.)
- **Providers**: Service registration and dependency injection
- **Foundation**: Framework customizations and bootstrapping

### Domain Layer (`src/Domain/`)
- **Actions**: Single-purpose business logic operations
- **Data**: Type-safe DTOs with validation rules
- **Models**: Eloquent models representing domain entities
- **Services**: Complex business logic that spans multiple entities

### Support Layer (`src/Support/`)
- **Base Classes**: Common functionality for controllers, models, etc.
- **Helpers**: Utility functions and domain helpers
- **Traits**: Reusable behavior across the application

## File Count Summary

- **DTOs**: `5 files`
- **Actions**: `5 files`
- **Models**: `1 files`
- **Controllers**: `10 files`
- **Tests**: `13 files`

## Frontend Structure

```
resources/
├── js/
│   ├── types/
│   │   └── ziggy.d.ts
│   │   └── globals.d.ts
│   │   └── index.d.ts
│   ├── lib/
│   │   └── utils.ts
│   ├── layouts/
│   │   └── AppLayout.vue
│   │   └── AppSidebarLayout.vue
│   │   └── AppHeaderLayout.vue
│   ├── composables/
│   │   └── useInitials.ts
│   │   └── useAppearance.ts
│   ├── components/
│   │   └── PlaceholderPattern.vue
│   │   └── AppSidebarHeader.vue
│   │   └── UserMenuContent.vue
│   ├── pages/
│   │   └── Welcome.vue
│   │   └── Appearance.vue
│   │   └── Password.vue
└── views/
    └── app.blade.php             # Main application template
```

## Key Architectural Benefits

✅ **Separation of Concerns**: Clear boundaries between layers
✅ **Testability**: Each layer can be tested independently
✅ **Maintainability**: Business logic is centralized in domain layer
✅ **Type Safety**: DTOs provide compile-time type checking
✅ **Scalability**: Easy to add new domains and features
