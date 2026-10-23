<?php

declare(strict_types=1);

use App\Models\User;
use Src\IdentityAccess\Domain\CredentialsVerifier;
use Src\IdentityAccess\Domain\Email;
use Src\IdentityAccess\Domain\InvalidCredentials;

beforeEach(function (): void {
    $this->user = User::factory()->create([
        'email' => 'test@prex.com',
        'password' => 'secret-password',
    ]);
    $this->verifier = $this->app->make(CredentialsVerifier::class);
});

test('eloquent verifier returns the user id for valid credentials', function (): void {
    $userId = $this->verifier->verify(new Email('test@prex.com'), 'secret-password');

    expect($userId->value())->toBe($this->user->id);
});

test('eloquent verifier rejects a wrong password', function (): void {
    $this->verifier->verify(new Email('test@prex.com'), 'wrong-password');
})->throws(InvalidCredentials::class);
