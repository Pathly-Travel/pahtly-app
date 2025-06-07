<?php

use Spatie\LaravelData\Exceptions\CannotCreateData;
use Src\Domain\Auth\Data\RegisterData;

test('it can be created with valid data', function () {
    $data = RegisterData::from([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect($data->name)->toBe('John Doe');
    expect($data->email)->toBe('john@example.com');
    expect($data->password)->toBe('password123');
    expect($data->passwordConfirmation)->toBe('password123');
});

test('it requires name parameter', function () {
    expect(fn () => RegisterData::from([
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]))->toThrow(CannotCreateData::class);
});

test('it requires email parameter', function () {
    expect(fn () => RegisterData::from([
        'name' => 'John Doe',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]))->toThrow(CannotCreateData::class);
});

test('it requires password parameter', function () {
    expect(fn () => RegisterData::from([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password_confirmation' => 'password123',
    ]))->toThrow(CannotCreateData::class);
});

test('it can be converted to array', function () {
    $data = RegisterData::from([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $array = $data->toArray();

    expect($array)->toHaveKeys(['name', 'email', 'password', 'password_confirmation']);
    expect($array['name'])->toBe('John Doe');
    expect($array['email'])->toBe('john@example.com');
    expect($array['password'])->toBe('password123');
    expect($array['password_confirmation'])->toBe('password123');
}); 