<?php

use App\Providers\AppServiceProvider;
use Src\Audit\Infrastructure\AuditServiceProvider;
use Src\Favorites\Infrastructure\FavoritesServiceProvider;
use Src\Gifs\Infrastructure\GifsServiceProvider;
use Src\IdentityAccess\Infrastructure\IdentityAccessServiceProvider;
use Src\Shared\Infrastructure\SharedServiceProvider;

return [
    AppServiceProvider::class,
    SharedServiceProvider::class,
    IdentityAccessServiceProvider::class,
    GifsServiceProvider::class,
    FavoritesServiceProvider::class,
    AuditServiceProvider::class,
];
