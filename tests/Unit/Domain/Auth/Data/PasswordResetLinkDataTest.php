<?php

use Spatie\LaravelData\Exceptions\CannotCreateData;
use Src\Domain\Auth\Data\PasswordResetLinkData;

test('it can be created with valid data', function () {
    $data = PasswordResetLinkData::from([
        'email' => 'test@example.com',
    ]);

    expect($data->email)->toBe('test@example.com');
});

test('it requires email parameter', function () {
    expect(fn () => PasswordResetLinkData::from([]))->toThrow(CannotCreateData::class);
});

test('it can be converted to array', function () {
    $data = PasswordResetLinkData::from([
        'email' => 'test@example.com',
    ]);

    $array = $data->toArray();

    expect($array)->toBe([
        'email' => 'test@example.com',
    ]);
}); 