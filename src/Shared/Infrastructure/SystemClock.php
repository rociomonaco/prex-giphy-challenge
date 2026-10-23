<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure;

use DateTimeImmutable;
use Src\Shared\Domain\Clock;

final class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable;
    }
}
