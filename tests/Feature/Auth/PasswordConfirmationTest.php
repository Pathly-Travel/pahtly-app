<?php

use Src\Domain\User\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/confirm-password');

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});

test('password confirmation requires password field', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/confirm-password', []);

    $response->assertSessionHasErrors('password');
});

test('password confirmation redirects to login when not authenticated', function () {
    $response = $this->post('/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertRedirect(route('login'));
});

test('password confirmation sets session timestamp on success', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/confirm-password', [
        'password' => 'password',
    ]);

    $this->assertNotNull(session('auth.password_confirmed_at'));
});
