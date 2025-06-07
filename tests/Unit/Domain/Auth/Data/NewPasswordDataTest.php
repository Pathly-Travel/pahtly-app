<?php

use Spatie\LaravelData\Exceptions\CannotCreateData;
use Src\Domain\Auth\Data\NewPasswordData;

test('it can be created with valid data', function () {
    $data = NewPasswordData::from([
        'token' => 'valid-token',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect($data->token)->toBe('valid-token');
    expect($data->email)->toBe('test@example.com');
    expect($data->password)->toBe('password123');
    expect($data->password_confirmation)->toBe('password123');
});

test('it requires token parameter', function () {
    expect(fn () => NewPasswordData::from([
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]))->toThrow(CannotCreateData::class);
});

test('it requires email parameter', function () {
    expect(fn () => NewPasswordData::from([
        'token' => 'valid-token',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]))->toThrow(CannotCreateData::class);
});

test('it requires password parameter', function () {
    expect(fn () => NewPasswordData::from([
        'token' => 'valid-token',
        'email' => 'test@example.com',
        'password_confirmation' => 'password123',
    ]))->toThrow(CannotCreateData::class);
});

test('it can be converted to array', function () {
    $data = NewPasswordData::from([
        'token' => 'valid-token',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $array = $data->toArray();

    expect($array)->toHaveKeys(['token', 'email', 'password', 'password_confirmation']);
    expect($array['token'])->toBe('valid-token');
    expect($array['email'])->toBe('test@example.com');
}); 