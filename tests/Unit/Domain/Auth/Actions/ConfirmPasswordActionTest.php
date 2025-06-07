<?php

use Illuminate\Validation\ValidationException;
use Src\Domain\Auth\Actions\ConfirmPasswordAction;
use Src\Domain\Auth\Data\ConfirmPasswordData;
use Src\Domain\User\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it successfully confirms password with correct password', function () {
    $user = User::factory()->create();

    $confirmPasswordData = ConfirmPasswordData::from([
        'password' => 'password',
    ]);

    $action = new ConfirmPasswordAction();
    $result = $action($user, $confirmPasswordData);

    expect($result)->toBeTrue();
});

test('it throws validation exception with incorrect password', function () {
    $user = User::factory()->create();

    $confirmPasswordData = ConfirmPasswordData::from([
        'password' => 'wrong-password',
    ]);

    $action = new ConfirmPasswordAction();

    expect(fn () => $action($user, $confirmPasswordData))->toThrow(ValidationException::class);
});

test('it uses correct validation error message', function () {
    $user = User::factory()->create();

    $confirmPasswordData = ConfirmPasswordData::from([
        'password' => 'wrong-password',
    ]);

    $action = new ConfirmPasswordAction();

    try {
        $action($user, $confirmPasswordData);
        expect(false)->toBeTrue(); // Should not reach here
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('password');
    }
});

test('it validates against the provided user account', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['password' => bcrypt('different-password')]);

    $confirmPasswordData = ConfirmPasswordData::from([
        'password' => 'password', // This is user1's password
    ]);

    $action = new ConfirmPasswordAction();

    // Should work for user1
    $result = $action($user1, $confirmPasswordData);
    expect($result)->toBeTrue();

    // Should fail for user2
    expect(fn () => $action($user2, $confirmPasswordData))->toThrow(ValidationException::class);
}); 