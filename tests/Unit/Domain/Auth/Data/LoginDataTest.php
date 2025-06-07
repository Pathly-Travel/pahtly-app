<?php

use Spatie\LaravelData\Exceptions\CannotCreateData;
use Src\Domain\Auth\Data\LoginData;

test('it can be created with valid data', function () {
    $data = LoginData::from([
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    expect($data->email)->toBe('test@example.com');
    expect($data->password)->toBe('password123');
    expect($data->remember)->toBeFalse();
});

test('it can be created with remember option', function () {
    $data = LoginData::from([
        'email' => 'test@example.com',
        'password' => 'password123',
        'remember' => true,
    ]);

    expect($data->remember)->toBeTrue();
});

test('it requires email parameter', function () {
    expect(fn () => LoginData::from([
        'password' => 'password123',
    ]))->toThrow(CannotCreateData::class);
});

test('it requires password parameter', function () {
    expect(fn () => LoginData::from([
        'email' => 'test@example.com',
    ]))->toThrow(CannotCreateData::class);
});

test('it can be converted to array', function () {
    $data = LoginData::from([
        'email' => 'test@example.com',
        'password' => 'password123',
        'remember' => true,
    ]);

    $array = $data->toArray();

    expect($array)->toBe([
        'email' => 'test@example.com',
        'password' => 'password123',
        'remember' => true,
    ]);
});

test('it can exclude remember from array', function () {
    $data = LoginData::from([
        'email' => 'test@example.com',
        'password' => 'password123',
        'remember' => true,
    ]);

    $array = $data->except('remember')->toArray();

    expect($array)->toBe([
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);
}); 