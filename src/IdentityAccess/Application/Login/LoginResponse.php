<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Application\Login;

use DateTimeImmutable;

final readonly class LoginResponse
{
    public function __construct(
        public string $accessToken,
        public int $expiresIn,
        public DateTimeImmutable $expiresAt,
        public int $userId,
        public string $tokenType = 'Bearer',
    ) {}
}
