<?php

declare(strict_types=1);

use Src\IdentityAccess\Domain\AccessToken;
use Src\IdentityAccess\Domain\InvalidAccessToken;

test('access token rejects an empty value', function (): void {
    new AccessToken('', new DateTimeImmutable('+30 minutes'));
})->throws(InvalidAccessToken::class);
