<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Infrastructure;

use Carbon\CarbonInterval;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Src\IdentityAccess\Domain\AccessToken;
use Src\IdentityAccess\Domain\CredentialsVerifier;
use Src\IdentityAccess\Domain\TokenIssuer;
use Src\IdentityAccess\Infrastructure\Passport\PassportTokenIssuer;
use Src\IdentityAccess\Infrastructure\Persistence\EloquentCredentialsVerifier;

final class IdentityAccessServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        CredentialsVerifier::class => EloquentCredentialsVerifier::class,
        TokenIssuer::class => PassportTokenIssuer::class,
    ];

    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    public function boot(): void
    {
        Passport::personalAccessTokensExpireIn(CarbonInterval::minutes(AccessToken::LIFETIME_MINUTES));
    }
}
