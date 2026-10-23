<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Application\Login;

use Src\IdentityAccess\Domain\AccessToken;
use Src\IdentityAccess\Domain\CredentialsVerifier;
use Src\IdentityAccess\Domain\Email;
use Src\IdentityAccess\Domain\InvalidCredentials;
use Src\IdentityAccess\Domain\InvalidEmail;
use Src\IdentityAccess\Domain\TokenIssuer;

final readonly class LoginHandler
{
    public function __construct(
        private CredentialsVerifier $credentialsVerifier,
        private TokenIssuer $tokenIssuer,
    ) {
    }

    /**
     * @throws InvalidEmail
     * @throws InvalidCredentials
     */
    public function handle(LoginCommand $command): LoginResponse
    {
        $userId = $this->credentialsVerifier->verify(new Email($command->email), $command->password);

        $token = $this->tokenIssuer->issueFor($userId);

        return new LoginResponse(
            accessToken: $token->value(),
            expiresIn: AccessToken::lifetimeInSeconds(),
            expiresAt: $token->expiresAt(),
        );
    }
}
