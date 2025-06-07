<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Src\Domain\Auth\Actions\LoginAction;
use Src\Domain\Auth\Data\LoginData;
use Src\Domain\User\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it successfully logs in user with valid credentials', function () {
    $user = User::factory()->create();

    $loginData = LoginData::from([
        'email' => $user->email,
        'password' => 'password',
        'remember' => false,
    ]);

    $action = new LoginAction();
    $result = $action($loginData);

    expect($result)->toBeTrue();
    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->id)->toBe($user->id);
});

test('it successfully logs in user with remember option', function () {
    $user = User::factory()->create();

    $loginData = LoginData::from([
        'email' => $user->email,
        'password' => 'password',
        'remember' => true,
    ]);

    $action = new LoginAction();
    $result = $action($loginData);

    expect($result)->toBeTrue();
    expect(Auth::check())->toBeTrue();
});

test('it throws validation exception with invalid credentials', function () {
    $user = User::factory()->create();

    $loginData = LoginData::from([
        'email' => $user->email,
        'password' => 'wrong-password',
        'remember' => false,
    ]);

    $action = new LoginAction();

    expect(fn () => $action($loginData))->toThrow(ValidationException::class);
});

test('it throws validation exception with non-existent user', function () {
    $loginData = LoginData::from([
        'email' => 'nonexistent@example.com',
        'password' => 'password',
        'remember' => false,
    ]);

    $action = new LoginAction();

    expect(fn () => $action($loginData))->toThrow(ValidationException::class);
}); 