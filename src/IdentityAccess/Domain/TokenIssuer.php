<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Domain;

use Src\Shared\Domain\UserId;

interface TokenIssuer
{
    public function issueFor(UserId $userId): AccessToken;
}
