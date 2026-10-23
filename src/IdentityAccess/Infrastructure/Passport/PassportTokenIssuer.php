<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Infrastructure\Passport;

use App\Models\User;
use RuntimeException;
use Src\IdentityAccess\Domain\AccessToken;
use Src\IdentityAccess\Domain\TokenIssuer;
use Src\Shared\Domain\UserId;

final class PassportTokenIssuer implements TokenIssuer
{
    private const TOKEN_NAME = 'login';

    public function issueFor(UserId $userId): AccessToken
    {
        $result = User::query()->findOrFail($userId->value())->createToken(self::TOKEN_NAME);

        $token = $result->getToken() ?? throw new RuntimeException('Issued access token was not persisted.');

        return new AccessToken($result->accessToken, $token->expires_at->toDateTimeImmutable());
    }
}
