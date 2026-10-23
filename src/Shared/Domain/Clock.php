<?php

declare(strict_types=1);

namespace Src\Shared\Domain;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
