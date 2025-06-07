<?php

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Src\Domain\User\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

test('password reset link request requires email', function () {
    $response = $this->post('/forgot-password', []);

    $response->assertSessionHasErrors('email');
});

test('password reset link request requires valid email', function () {
    $response = $this->post('/forgot-password', [
        'email' => 'invalid-email',
    ]);

    $response->assertSessionHasErrors('email');
});

test('password reset requires token', function () {
    $user = User::factory()->create();

    $response = $this->post('/reset-password', [
        'email' => $user->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('token');
});

test('password reset requires email', function () {
    $response = $this->post('/reset-password', [
        'token' => 'fake-token',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
});

test('password reset requires valid email', function () {
    $response = $this->post('/reset-password', [
        'token' => 'fake-token',
        'email' => 'invalid-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
});

test('password reset requires password', function () {
    $user = User::factory()->create();

    $response = $this->post('/reset-password', [
        'token' => 'fake-token',
        'email' => $user->email,
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('password');
});

test('password reset requires password confirmation', function () {
    $user = User::factory()->create();

    $response = $this->post('/reset-password', [
        'token' => 'fake-token',
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('password');
});

test('password reset requires matching password confirmation', function () {
    $user = User::factory()->create();

    $response = $this->post('/reset-password', [
        'token' => 'fake-token',
        'email' => $user->email,
        'password' => 'password',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors('password');
});

test('password reset requires minimum password length', function () {
    $user = User::factory()->create();

    $response = $this->post('/reset-password', [
        'token' => 'fake-token',
        'email' => $user->email,
        'password' => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
});
