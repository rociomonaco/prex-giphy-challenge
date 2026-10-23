<?php

declare(strict_types=1);

namespace Tests\Support\Fakes;

use DateTimeImmutable;
use Src\Shared\Domain\Clock;

final class FixedClock implements Clock
{
    public function __construct(private readonly DateTimeImmutable $instant) {}

    public function now(): DateTimeImmutable
    {
        return $this->instant;
    }
}
