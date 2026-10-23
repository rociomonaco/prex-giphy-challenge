<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Application\Login;

final readonly class LoginCommand
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}
