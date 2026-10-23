<?php

declare(strict_types=1);

namespace Tests\Support\Fakes;

use Src\IdentityAccess\Domain\CredentialsVerifier;
use Src\IdentityAccess\Domain\Email;
use Src\IdentityAccess\Domain\InvalidCredentials;
use Src\Shared\Domain\UserId;

final readonly class InMemoryCredentialsVerifier implements CredentialsVerifier
{
    /**
     * @param array<string, array{int, string}> $users email => [id, plainPassword]
     */
    public function __construct(private array $users = [])
    {
    }

    public function verify(Email $email, string $plainPassword): UserId
    {
        $user = $this->users[$email->value()] ?? null;

        if ($user === null || $user[1] !== $plainPassword) {
            throw new InvalidCredentials();
        }

        return new UserId($user[0]);
    }
}
