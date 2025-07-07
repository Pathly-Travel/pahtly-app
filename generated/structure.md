# Project Structure (Auto-generated)

**Generated:** 2025-06-07 13:50:33 UTC

## Domain Driven Design Architecture

This application follows Domain Driven Design (DDD) principles with a clean three-layer architecture:

```
src/
├── App/                          # Application Layer (HTTP/Infrastructure)
│   ├── Foundation/
│   │   └── Application.php       # Custom Laravel Application
│   ├── Portal/                   # HTTP Controllers grouped by domain
│   │   ├── Settings/
│   │   │   └── ProfileController.php
│   │   │   └── PasswordController.php
│   │   ├── Auth/
│   │   │   └── NewPasswordController.php
│   │   │   └── EmailVerificationPromptController.php
│   │   │   └── VerifyEmailController.php
│   ├── Middleware/               # HTTP Middleware
│   │   └── HandleInertiaRequests.php
│   │   └── HandleAppearance.php
│   └── Providers/
│       └── AppServiceProvider.php
│       └── DomainServiceProvider.php
├── Domain/                       # Domain Layer (Business Logic)
│   ├── Settings/                     # Settings Domain
│   │   ├── Actions/
│   │   │   ├── UpdatePasswordAction.php
│   │   ├── Data/
│   │   │   ├── PasswordUpdateData.php
│   │   │   ├── ProfileUpdateData.php
│   ├── Auth/                     # Auth Domain
│   │   ├── Actions/
│   │   │   ├── SendPasswordResetLinkAction.php
│   │   │   ├── ConfirmPasswordAction.php
│   │   │   ├── LoginAction.php
│   │   ├── Data/
│   │   │   ├── RegisterData.php
│   │   │   ├── LoginData.php
│   │   │   ├── PasswordResetLinkData.php
│   ├── User/                     # User Domain
│   │   ├── Actions/
│   │   │   ├── UpdateUserProfileAction.php
│   │   │   ├── DeleteUserAction.php
│   │   ├── Data/
│   │   │   ├── UserData.php
│   │   └── Models/
│   │       └── User.php
└── Support/                      # Support Layer (Shared Infrastructure)
    ├── Traits/
    │   └── HasDomainData.php
    ├── Controllers/
    │   └── Controller.php
    ├── Helpers/
    │   └── DomainHelper.php
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

- **DTOs**:        8 files
- **Actions**:        8 files  
- **Models**:        1 files
- **Controllers**:       10 files
- **Tests**:       22 files

## Frontend Structure

```
resources/
├── js/
│   ├── types/
│   │   └── globals.d.ts
│   │   └── ziggy.d.ts
│   │   └── index.d.ts
│   ├── composables/
│   │   └── useInitials.ts
│   │   └── useAppearance.ts
│   ├── components/
│   │   └── AppSidebarHeader.vue
│   │   └── SidebarMenuSubButton.vue
│   │   └── SidebarContent.vue
│   ├── layouts/
│   │   └── Layout.vue
│   │   └── AuthLayout.vue
│   │   └── AppSidebarLayout.vue
│   ├── lib/
│   │   └── utils.ts
│   ├── pages/
│   │   └── Profile.vue
│   │   └── Password.vue
│   │   └── Appearance.vue
└── views/
    └── app.blade.php             # Main application template
```

## Key Architectural Benefits

✅ **Separation of Concerns**: Clear boundaries between layers
✅ **Testability**: Each layer can be tested independently  
✅ **Maintainability**: Business logic is centralized in domain layer
✅ **Type Safety**: DTOs provide compile-time type checking
✅ **Scalability**: Easy to add new domains and features

