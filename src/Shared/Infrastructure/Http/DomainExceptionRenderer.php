<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Http;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Src\Favorites\Domain\FavoriteAlreadyExists;
use Src\Gifs\Domain\GifNotFound;
use Src\Gifs\Domain\GifProviderUnavailable;
use Src\IdentityAccess\Domain\InvalidCredentials;
use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\InvalidArgument;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

final class DomainExceptionRenderer
{
    /**
     * Checked in order with instanceof: specific classes must come before their parents.
     *
     * @var array<class-string<DomainException>, array{int, string}>
     */
    private const MAP = [
        InvalidCredentials::class => [401, 'invalid_credentials'],
        GifNotFound::class => [404, 'gif_not_found'],
        FavoriteAlreadyExists::class => [409, 'favorite_already_exists'],
        GifProviderUnavailable::class => [502, 'gif_provider_unavailable'],
        InvalidArgument::class => [422, 'invalid_argument'],
    ];

    public function render(Throwable $exception): ?JsonResponse
    {
        if ($exception instanceof AuthenticationException) {
            return $this->error(401, 'unauthenticated', 'Missing, invalid or expired token.');
        }

        if ($exception instanceof AuthorizationException || $exception instanceof AccessDeniedHttpException) {
            $message = $exception->getMessage();

            if ($message === '' || $message === 'This action is unauthorized.') {
                $message = 'You cannot save favorites for another user.';
            }

            return $this->error(403, 'forbidden', $message);
        }

        if ($exception instanceof ValidationException) {
            return $this->error(422, 'validation_error', 'The given data is invalid.', $exception->errors());
        }

        if (! $exception instanceof DomainException) {
            return null;
        }

        foreach (self::MAP as $class => [$status, $code]) {
            if ($exception instanceof $class) {
                return $this->error($status, $code, $exception->getMessage());
            }
        }

        return $this->error(500, 'internal_error', 'Unexpected error.');
    }

    /**
     * @param array<string, list<string>>|null $details
     */
    private function error(int $status, string $code, string $message, ?array $details = null): JsonResponse
    {
        $error = ['code' => $code, 'message' => $message];

        if ($details !== null) {
            $error['details'] = $details;
        }

        return new JsonResponse(['error' => $error], $status);
    }
}
