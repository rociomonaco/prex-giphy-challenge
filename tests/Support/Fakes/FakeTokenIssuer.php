<?php

declare(strict_types=1);

namespace Tests\Support\Fakes;

use DateTimeImmutable;
use Src\IdentityAccess\Domain\AccessToken;
use Src\IdentityAccess\Domain\TokenIssuer;
use Src\Shared\Domain\UserId;

final class FakeTokenIssuer implements TokenIssuer
{
    public const TOKEN = 'fake-access-token';

    private int $issuedCount = 0;

    public function __construct(
        private readonly DateTimeImmutable $now = new DateTimeImmutable(),
    ) {
    }

    public function issueFor(UserId $userId): AccessToken
    {
        $this->issuedCount++;

        return new AccessToken(
            self::TOKEN,
            $this->now->modify(sprintf('+%d minutes', AccessToken::LIFETIME_MINUTES)),
        );
    }

    public function issuedCount(): int
    {
        return $this->issuedCount;
    }
}
