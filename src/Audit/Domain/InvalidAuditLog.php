<?php

declare(strict_types=1);

namespace Src\Audit\Domain;

use Src\Shared\Domain\InvalidArgument;

final class InvalidAuditLog extends InvalidArgument
{
    public static function status(int $status): self
    {
        return new self(sprintf('HTTP status must be between 100 and 599, got %d.', $status));
    }
}
