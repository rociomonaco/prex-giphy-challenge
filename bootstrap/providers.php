<?php

use App\Providers\AppServiceProvider;
use Src\Gifs\Infrastructure\GifsServiceProvider;
use Src\IdentityAccess\Infrastructure\IdentityAccessServiceProvider;

return [
    AppServiceProvider::class,
    IdentityAccessServiceProvider::class,
    GifsServiceProvider::class,
];
