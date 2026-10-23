<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Domain;

use Src\Shared\Domain\DomainException;

final class InvalidCredentials extends DomainException
{
    public function __construct()
    {
        parent::__construct('Invalid email or password.');
    }
}
