<?php

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Src\Domain\Auth\Actions\RegisterAction;
use Src\Domain\Auth\Data\RegisterData;
use Src\Domain\User\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it creates a new user with valid data', function () {
    Event::fake();

    $registerData = RegisterData::from([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $action = new RegisterAction();
    $user = $action($registerData);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
    expect(Hash::check('password123', $user->password))->toBeTrue();

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

test('it fires registered event when user is created', function () {
    Event::fake();

    $registerData = RegisterData::from([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $action = new RegisterAction();
    $user = $action($registerData);

    Event::assertDispatched(Registered::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

test('it hashes the password correctly', function () {
    Event::fake();

    $registerData = RegisterData::from([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $action = new RegisterAction();
    $user = $action($registerData);

    expect($user->password)->not->toBe('password123');
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('it returns the created user instance', function () {
    Event::fake();

    $registerData = RegisterData::from([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $action = new RegisterAction();
    $user = $action($registerData);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->exists)->toBeTrue();
    expect($user->id)->not->toBeNull();
}); 