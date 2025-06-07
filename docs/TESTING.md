# Testing Guide

## Overview

This guide covers testing strategies for a Laravel DTO application built with Domain Driven Design principles. We use Pest PHP for a modern, expressive testing experience.

## Testing Philosophy

Our testing approach follows the **Testing Pyramid**:

```mermaid
graph TB
    subgraph "Testing Pyramid"
        A[Unit Tests<br/>Fast, Isolated, Many]
        B[Integration Tests<br/>Medium Speed, Some Dependencies]
        C[Feature Tests<br/>Slow, End-to-End, Few]
    end
    
    A --> B◊
    B --> C
```

### Test Categories

1. **Unit Tests**: Test individual DTOs, Actions, and Models in isolation
2. **Integration Tests**: Test how components work together
3. **Feature Tests**: Test complete user workflows through HTTP endpoints

## Test Structure

### Directory Organization

```
tests/
├── Feature/                    # HTTP endpoint tests
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegisterTest.php
│   └── Settings/
│       ├── ProfileTest.php
│       └── PasswordTest.php
├── Unit/                       # Isolated component tests
│   ├── Actions/
│   │   ├── User/
│   │   │   ├── CreateUserActionTest.php
│   │   │   └── UpdateUserProfileActionTest.php
│   │   └── Auth/
│   │       └── LoginActionTest.php
│   ├── Data/
│   │   ├── UserDataTest.php
│   │   └── ProfileUpdateDataTest.php
│   └── Models/
│       └── UserTest.php
├── Pest.php                    # Pest configuration
└── TestCase.php               # Base test class
```

## Testing DTOs (Data Transfer Objects)

### Basic DTO Testing

DTOs should be tested for:
- Data transformation
- Validation rules
- Type safety
- Factory methods

```php
<?php

use Src\Domain\Settings\Data\ProfileUpdateData;
use Spatie\LaravelData\Exceptions\ValidationException;

describe('ProfileUpdateData', function () {
    it('creates from valid array', function () {
        $data = ProfileUpdateData::from([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        
        expect($data)
            ->name->toBe('John Doe')
            ->email->toBe('john@example.com')
            ->userId->toBeNull();
    });
    
    it('creates from request', function () {
        $request = request();
        $request->merge([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);
        
        $data = ProfileUpdateData::from($request);
        
        expect($data)
            ->name->toBe('Jane Smith')
            ->email->toBe('jane@example.com');
    });
    
    it('validates required fields', function () {
        ProfileUpdateData::from([
            'email' => 'john@example.com',
            // Missing required 'name' field
        ]);
    })->throws(ValidationException::class, 'The name field is required');
    
    it('validates email format', function () {
        ProfileUpdateData::from([
            'name' => 'John Doe',
            'email' => 'invalid-email',
        ]);
    })->throws(ValidationException::class);
    
    it('converts to array', function () {
        $data = ProfileUpdateData::from([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        
        expect($data->toArray())->toBe([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'userId' => null,
        ]);
    });
    
    it('excludes specific fields', function () {
        $data = ProfileUpdateData::from([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'userId' => 123,
        ]);
        
        $filtered = $data->except('userId');
        
        expect($filtered->toArray())->toBe([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    });
});
```

### Complex DTO Testing

For DTOs with custom validation or factory methods:

```php
<?php

use Src\Domain\Auth\Data\RegisterData;

describe('RegisterData', function () {
    it('validates password confirmation', function () {
        RegisterData::from([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ]);
    })->throws(ValidationException::class, 'password confirmation does not match');
    
    it('validates password strength', function () {
        RegisterData::from([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123', // Too short
            'password_confirmation' => '123',
        ]);
    })->throws(ValidationException::class, 'at least 8 characters');
    
    it('creates from registration form', function () {
        $formData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms_accepted' => true,
        ];
        
        $data = RegisterData::fromRegistrationForm($formData);
        
        expect($data)
            ->name->toBe('John Doe')
            ->email->toBe('john@example.com')
            ->termsAccepted->toBeTrue();
    });
});
```

## Testing Actions

### Basic Action Testing

Actions should be tested for:
- Business logic correctness
- Input/output behavior
- Exception handling
- Side effects

```php
<?php

use Src\Domain\User\Actions\UpdateUserProfileAction;
use Src\Domain\Settings\Data\ProfileUpdateData;
use Src\Domain\User\Models\User;

describe('UpdateUserProfileAction', function () {
    it('updates user profile successfully', function () {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'email_verified_at' => now(),
        ]);
        
        $data = ProfileUpdateData::from([
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
        
        $action = new UpdateUserProfileAction();
        $updatedUser = $action($user, $data);
        
        expect($updatedUser)
            ->name->toBe('New Name')
            ->email->toBe('new@example.com')
            ->email_verified_at->toBeNull(); // Reset when email changes
            
        expect($updatedUser->fresh())
            ->name->toBe('New Name')
            ->email->toBe('new@example.com');
    });
    
    it('preserves email verification when email unchanged', function () {
        $verifiedAt = now();
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => $verifiedAt,
        ]);
        
        $data = ProfileUpdateData::from([
            'name' => 'John Smith', // Only name changed
            'email' => 'john@example.com',
        ]);
        
        $action = new UpdateUserProfileAction();
        $updatedUser = $action($user, $data);
        
        expect($updatedUser)
            ->name->toBe('John Smith')
            ->email_verified_at->toEqual($verifiedAt);
    });
    
    it('handles database errors gracefully', function () {
        $user = User::factory()->create();
        
        // Mock database failure
        DB::shouldReceive('transaction')
            ->andThrow(new \Exception('Database connection failed'));
        
        $data = ProfileUpdateData::from([
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
        
        $action = new UpdateUserProfileAction();
        
        expect(fn() => $action($user, $data))
            ->toThrow(\Exception::class, 'Database connection failed');
    });
});
```

## Feature Testing (HTTP Endpoints)

### Authentication Feature Tests

```php
<?php

describe('Authentication API', function () {
    it('registers new user successfully', function () {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                ],
                'token',
                'message',
            ])
            ->assertJson([
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
                'message' => 'User registered successfully',
            ]);
        
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    });
    
    it('validates registration data', function () {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ]);
    });
    
    it('prevents duplicate email registration', function () {
        User::factory()->create(['email' => 'existing@example.com']);
        
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });
});
```

## Best Practices

### Test Organization

1. **Group related tests**: Use `describe()` blocks to group related test cases
2. **Use descriptive test names**: Test names should clearly describe what is being tested
3. **Follow AAA pattern**: Arrange, Act, Assert in each test
4. **Keep tests independent**: Each test should be able to run in isolation

### Test Data Management

1. **Use factories for model creation**: Leverage Laravel's model factories
2. **Create minimal test data**: Only create the data needed for each test
3. **Use database transactions**: Wrap tests in transactions for faster cleanup
4. **Separate test data from production data**: Use separate test database

### Mocking and Stubbing

1. **Mock external dependencies**: Mock APIs, file systems, and other external services
2. **Use dependency injection**: Makes mocking easier in actions
3. **Don't mock DTOs**: DTOs should be tested with real data
4. **Mock at the right level**: Mock at the boundary of your domain

### Performance Considerations

1. **Use in-memory databases for unit tests**: SQLite in-memory for speed
2. **Parallel test execution**: Use Pest's parallel testing features
3. **Profile slow tests**: Identify and optimize slow test cases
4. **Cache test dependencies**: Cache Composer and NPM dependencies in CI 