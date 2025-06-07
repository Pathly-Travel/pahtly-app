<?php

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Src\Domain\Auth\Actions\SendPasswordResetLinkAction;
use Src\Domain\Auth\Data\PasswordResetLinkData;
use Src\Domain\User\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it sends password reset link to existing user', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'john@example.com']);

    $passwordResetLinkData = PasswordResetLinkData::from([
        'email' => 'john@example.com',
    ]);

    $action = new SendPasswordResetLinkAction();
    $status = $action($passwordResetLinkData);

    expect($status)->toBe(Password::RESET_LINK_SENT);
    Notification::assertSentTo($user, ResetPassword::class);
});

test('it returns appropriate status for non-existent user', function () {
    Notification::fake();

    $passwordResetLinkData = PasswordResetLinkData::from([
        'email' => 'nonexistent@example.com',
    ]);

    $action = new SendPasswordResetLinkAction();
    $status = $action($passwordResetLinkData);

    // Laravel returns INVALID_USER for non-existent users
    expect($status)->toBe(Password::INVALID_USER);
    Notification::assertNothingSent();
});

test('it uses the correct data format', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'john@example.com']);

    $passwordResetLinkData = PasswordResetLinkData::from([
        'email' => 'john@example.com',
    ]);

    $action = new SendPasswordResetLinkAction();
    $action($passwordResetLinkData);

    Notification::assertSentTo($user, ResetPassword::class);
}); 