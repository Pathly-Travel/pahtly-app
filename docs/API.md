# API Documentation

## Overview

This Laravel DTO application provides a clean REST API built with Domain Driven Design principles. All endpoints use DTOs for type-safe data handling and Actions for business logic.

## Authentication

The API uses Laravel Sanctum for authentication. Include the session cookie or API token in requests.

### Session Authentication
```http
GET /api/user
Cookie: laravel_session=your_session_token
```

### Token Authentication
```http
GET /api/user
Authorization: Bearer your_api_token
```

## Base URL

```
Development: http://localhost:8000
Production: https://your-domain.com
```

## Response Format

All API responses follow a consistent format:

### Success Response
```json
{
    "data": {
        // Response data
    },
    "message": "Operation successful",
    "status": "success"
}
```

### Error Response
```json
{
    "message": "Validation failed",
    "errors": {
        "field_name": [
            "Error message"
        ]
    },
    "status": "error"
}
```

## Authentication Endpoints

### Register User

Create a new user account.

**Endpoint:** `POST /api/auth/register`

**DTO:** `RegisterData`

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**DTO Definition:**
```php
<?php

namespace Src\Domain\Auth\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Confirmed;

class RegisterData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $name,
        
        #[Required, Email, Unique('users', 'email')]
        public string $email,
        
        #[Required, Min(8), Confirmed]
        public string $password,
    ) {}
}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    "token": "1|abc123...",
    "message": "User registered successfully"
}
```

**Validation Errors:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email has already been taken."
        ],
        "password": [
            "The password must be at least 8 characters."
        ]
    }
}
```

### Login User

Authenticate a user and receive an API token.

**Endpoint:** `POST /api/auth/login`

**DTO:** `LoginData`

**Request:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**DTO Definition:**
```php
<?php

namespace Src\Domain\Auth\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;

class LoginData extends Data
{
    public function __construct(
        #[Required, Email]
        public string $email,
        
        #[Required]
        public string $password,
    ) {}
}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": "2024-01-15T10:30:00.000000Z"
    },
    "token": "2|def456...",
    "message": "Login successful"
}
```

**Error Response:**
```json
{
    "message": "Invalid credentials",
    "status": "error"
}
```

### Logout User

Revoke the current API token.

**Endpoint:** `POST /api/auth/logout`

**Authentication:** Required

**Response:**
```json
{
    "message": "Logged out successfully"
}
```

## User Management Endpoints

### Get Current User

Retrieve the authenticated user's profile.

**Endpoint:** `GET /api/user`

**Authentication:** Required

**Response:**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": "2024-01-15T10:30:00.000000Z",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### Update User Profile

Update the authenticated user's profile information.

**Endpoint:** `PATCH /api/profile`

**Authentication:** Required

**DTO:** `ProfileUpdateData`

**Request:**
```json
{
    "name": "John Smith",
    "email": "johnsmith@example.com"
}
```

**DTO Definition:**
```php
<?php

namespace Src\Domain\Settings\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;

class ProfileUpdateData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $name,
        
        #[Required, Email, Max(255)]
        public string $email,
        
        public ?int $userId = null,
    ) {}
}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "name": "John Smith",
        "email": "johnsmith@example.com",
        "email_verified_at": null,
        "updated_at": "2024-01-15T11:30:00.000000Z"
    },
    "message": "Profile updated successfully"
}
```

**Business Rules:**
- If email is changed, `email_verified_at` is set to `null`
- Name must be unique per user account
- Email must be unique across all users

### Update Password

Change the authenticated user's password.

**Endpoint:** `PATCH /api/password`

**Authentication:** Required

**DTO:** `PasswordUpdateData`

**Request:**
```json
{
    "current_password": "oldpassword123",
    "password": "newpassword456",
    "password_confirmation": "newpassword456"
}
```

**DTO Definition:**
```php
<?php

namespace Src\Domain\Settings\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Confirmed;

class PasswordUpdateData extends Data
{
    public function __construct(
        #[Required]
        public string $current_password,
        
        #[Required, Min(8), Confirmed]
        public string $password,
    ) {}
}
```

**Response:**
```json
{
    "message": "Password updated successfully"
}
```

**Error Response:**
```json
{
    "message": "The current password is incorrect.",
    "errors": {
        "current_password": [
            "The current password is incorrect."
        ]
    }
}
```

### Delete User Account

Delete the authenticated user's account.

**Endpoint:** `DELETE /api/user`

**Authentication:** Required

**Request:**
```json
{
    "password": "currentpassword123"
}
```

**Response:**
```json
{
    "message": "Account deleted successfully"
}
```

**Business Rules:**
- Password confirmation required
- All user data is permanently deleted
- API tokens are revoked
- Action is irreversible

## Data Validation

### Common Validation Rules

All DTOs use Laravel's validation rules via attributes:

| Attribute | Rule | Description |
|-----------|------|-------------|
| `Required` | `required` | Field must be present and not empty |
| `Email` | `email` | Field must be a valid email address |
| `Max(n)` | `max:n` | Field must not exceed n characters |
| `Min(n)` | `min:n` | Field must be at least n characters |
| `Unique(table, column)` | `unique:table,column` | Field must be unique in database |
| `Confirmed` | `confirmed` | Field must have a matching `_confirmation` field |

### Custom Validation

DTOs can include custom validation logic:

```php
<?php

class UserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
    
    public static function rules(): array
    {
        return [
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            ],
        ];
    }
    
    public function messages(): array
    {
        return [
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, and one digit.',
        ];
    }
}
```

## Error Handling

### HTTP Status Codes

| Code | Description | When Used |
|------|-------------|-----------|
| `200` | OK | Successful GET, PATCH requests |
| `201` | Created | Successful POST requests |
| `204` | No Content | Successful DELETE requests |
| `400` | Bad Request | Invalid request format |
| `401` | Unauthorized | Authentication required |
| `403` | Forbidden | Insufficient permissions |
| `404` | Not Found | Resource not found |
| `422` | Unprocessable Entity | Validation failed |
| `500` | Internal Server Error | Server error |

### Domain Exceptions

The API translates domain exceptions to appropriate HTTP responses:

```php
<?php

// Domain exception
throw new UserNotFoundException("User not found");

// HTTP response
{
    "message": "User not found",
    "status": "error"
}
```

### Validation Errors

Validation errors return detailed field-level feedback:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "The name field is required."
        ],
        "email": [
            "The email must be a valid email address.",
            "The email has already been taken."
        ],
        "password": [
            "The password must be at least 8 characters.",
            "The password confirmation does not match."
        ]
    }
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse:

| Endpoint Type | Limit | Window |
|---------------|-------|--------|
| Authentication | 5 requests | 1 minute |
| General API | 60 requests | 1 minute |
| Password Reset | 3 requests | 1 minute |

**Rate Limit Headers:**
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1642248000
```

**Rate Limit Exceeded:**
```json
{
    "message": "Too Many Attempts.",
    "status": "error",
    "retry_after": 60
}
```

## Request Examples

### cURL Examples

**Register User:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login User:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Update Profile:**
```bash
curl -X PATCH http://localhost:8000/api/profile \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer your_token_here" \
  -d '{
    "name": "John Smith",
    "email": "johnsmith@example.com"
  }'
```

### JavaScript Examples

**Using Fetch API:**
```javascript
// Register user
const response = await fetch('/api/auth/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password123',
        password_confirmation: 'password123'
    })
});

const data = await response.json();
```

**Using Axios:**
```javascript
// Login user
const response = await axios.post('/api/auth/login', {
    email: 'john@example.com',
    password: 'password123'
});

// Store token for future requests
localStorage.setItem('api_token', response.data.token);

// Set default authorization header
axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
```

## Testing the API

### Automated Testing

The application includes comprehensive API tests:

```php
<?php

// Test user registration
it('registers a new user', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email'],
            'token',
            'message'
        ]);
});

// Test profile update
it('updates user profile', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->patchJson('/api/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    
    $response->assertStatus(200);
    
    expect($user->fresh())
        ->name->toBe('Updated Name')
        ->email->toBe('updated@example.com');
});
```

### Manual Testing

Use tools like Postman, Insomnia, or curl to test the API endpoints manually. Import the provided collection for quick setup.

## Postman Collection

```json
{
    "info": {
        "name": "Laravel DTO API",
        "description": "API endpoints for Laravel DTO application"
    },
    "auth": {
        "type": "bearer",
        "bearer": [
            {
                "key": "token",
                "value": "{{api_token}}",
                "type": "string"
            }
        ]
    },
    "variable": [
        {
            "key": "base_url",
            "value": "http://localhost:8000/api"
        },
        {
            "key": "api_token",
            "value": ""
        }
    ]
}
```

## Security Considerations

### Input Sanitization
- All input is validated through DTOs
- SQL injection prevention via Eloquent ORM
- XSS protection through output escaping

### Authentication Security
- Passwords are hashed using bcrypt
- API tokens are securely generated
- Session fixation protection enabled

### CORS Configuration
```php
// config/cors.php
'allowed_origins' => [
    'http://localhost:3000',  // React dev server
    'https://yourapp.com',    // Production frontend
],
'allowed_methods' => ['GET', 'POST', 'PATCH', 'DELETE'],
'allowed_headers' => ['Content-Type', 'Authorization', 'X-CSRF-TOKEN'],
```

### HTTPS Requirements
- All production endpoints require HTTPS
- Secure cookie settings for session management
- HSTS headers for security

## Versioning

The API follows semantic versioning:

- **v1**: Current stable version
- **v2**: Future version with breaking changes

Version is specified in the URL:
```
/api/v1/users
/api/v2/users
```

## Changelog

### v1.0.0 (Current)
- Initial API release
- User authentication and management
- Profile and password management
- Comprehensive validation and error handling 