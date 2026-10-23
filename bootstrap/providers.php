<?php

use App\Providers\AppServiceProvider;
use Src\IdentityAccess\Infrastructure\IdentityAccessServiceProvider;

return [
    AppServiceProvider::class,
    IdentityAccessServiceProvider::class,
];
