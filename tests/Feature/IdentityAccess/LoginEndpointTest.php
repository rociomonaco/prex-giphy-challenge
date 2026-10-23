<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function (): void {
    $this->createPersonalAccessClient();
    User::factory()->create(['email' => 'test@prex.com', 'password' => 'password']);
});

test('login endpoint returns 200 with a bearer token', function (): void {
    $response = $this->postJson(route('auth.login'), [
        'email' => 'test@prex.com',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token_type', 'access_token', 'expires_in', 'expires_at'])
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('expires_in', 1800);

    expect(DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $response->json('expires_at')))
        ->toBeInstanceOf(DateTimeImmutable::class);
});

test('login endpoint returns 401 with invalid credentials', function (): void {
    $this->postJson(route('auth.login'), [
        'email' => 'test@prex.com',
        'password' => 'wrong-password',
    ])
        ->assertUnauthorized()
        ->assertExactJson([
            'error' => [
                'code' => 'invalid_credentials',
                'message' => 'Invalid email or password.',
            ],
        ]);
});

test('login endpoint returns 422 when input is invalid', function (array $payload, string $field): void {
    $this->postJson(route('auth.login'), $payload)
        ->assertUnprocessable()
        ->assertJsonPath('error.code', 'validation_error')
        ->assertJsonPath('error.message', 'The given data is invalid.')
        ->assertJsonValidationErrors($field, 'error.details');
})->with([
    'missing email' => [['password' => 'password'], 'email'],
    'invalid email' => [['email' => 'not-an-email', 'password' => 'password'], 'email'],
    'missing password' => [['email' => 'test@prex.com'], 'password'],
]);
