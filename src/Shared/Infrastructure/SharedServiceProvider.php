<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Src\Shared\Domain\Clock;

final class SharedServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        Clock::class => SystemClock::class,
    ];
}
