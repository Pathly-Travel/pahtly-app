<?php

use Spatie\LaravelData\Exceptions\CannotCreateData;
use Src\Domain\Auth\Data\ConfirmPasswordData;

test('it can be created with valid data', function () {
    $data = ConfirmPasswordData::from([
        'password' => 'password123',
    ]);

    expect($data->password)->toBe('password123');
});

test('it requires password parameter', function () {
    expect(fn () => ConfirmPasswordData::from([]))->toThrow(CannotCreateData::class);
});

test('it can be converted to array', function () {
    $data = ConfirmPasswordData::from([
        'password' => 'password123',
    ]);

    $array = $data->toArray();

    expect($array)->toBe([
        'password' => 'password123',
    ]);
}); 