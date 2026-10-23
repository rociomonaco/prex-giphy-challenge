<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Domain;

use DateTimeImmutable;

final readonly class AccessToken
{
    public const LIFETIME_MINUTES = 30;

    public function __construct(
        private string $value,
        private DateTimeImmutable $expiresAt,
    ) {
        if (trim($value) === '') {
            throw new InvalidAccessToken();
        }
    }

    public static function lifetimeInSeconds(): int
    {
        return self::LIFETIME_MINUTES * 60;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
