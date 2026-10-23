<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Infrastructure\Http;

use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Src\IdentityAccess\Application\Login\LoginCommand;
use Src\IdentityAccess\Application\Login\LoginHandler;

final readonly class LoginController
{
    public function __construct(private LoginHandler $handler) {}

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $response = $this->handler->handle(new LoginCommand(
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
        ));

        // FormRequest is copied from the kernel request. terminate() sees the original.
        $request->attributes->set('audit.user_id', $response->userId);
        request()->attributes->set('audit.user_id', $response->userId);

        return new JsonResponse([
            'token_type' => $response->tokenType,
            'access_token' => $response->accessToken,
            'expires_in' => $response->expiresIn,
            'expires_at' => $response->expiresAt->format(DateTimeInterface::ATOM),
        ]);
    }
}
