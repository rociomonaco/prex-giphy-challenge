<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Domain;

use Src\Shared\Domain\UserId;

interface CredentialsVerifier
{
    /**
     * @throws InvalidCredentials
     */
    public function verify(Email $email, string $plainPassword): UserId;
}
