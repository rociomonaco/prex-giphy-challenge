<?php

declare(strict_types=1);

use Src\IdentityAccess\Application\Login\LoginCommand;
use Src\IdentityAccess\Application\Login\LoginHandler;
use Src\IdentityAccess\Domain\InvalidCredentials;
use Tests\Support\Fakes\FakeTokenIssuer;
use Tests\Support\Fakes\InMemoryCredentialsVerifier;

beforeEach(function (): void {
    $this->now = new DateTimeImmutable('2026-01-01 12:00:00');
    $this->tokenIssuer = new FakeTokenIssuer($this->now);
    $this->handler = new LoginHandler(
        new InMemoryCredentialsVerifier(['test@prex.com' => [1, 'password']]),
        $this->tokenIssuer,
    );
});

test('login returns a bearer token that expires in 30 minutes', function (): void {
    $response = $this->handler->handle(new LoginCommand('test@prex.com', 'password'));

    expect($response->tokenType)->toBe('Bearer')
        ->and($response->accessToken)->toBe(FakeTokenIssuer::TOKEN)
        ->and($response->expiresIn)->toBe(1800)
        ->and($response->expiresAt)->toEqual($this->now->modify('+30 minutes'));
});

test('login fails when the password is wrong', function (): void {
    $this->handler->handle(new LoginCommand('test@prex.com', 'wrong-password'));
})->throws(InvalidCredentials::class, 'Invalid email or password.');

test('login fails when the email does not exist', function (): void {
    $this->handler->handle(new LoginCommand('unknown@prex.com', 'password'));
})->throws(InvalidCredentials::class, 'Invalid email or password.');

test('no token is issued when credentials are invalid', function (): void {
    try {
        $this->handler->handle(new LoginCommand('test@prex.com', 'wrong-password'));
    } catch (InvalidCredentials) {
    }

    expect($this->tokenIssuer->issuedCount())->toBe(0);
});
