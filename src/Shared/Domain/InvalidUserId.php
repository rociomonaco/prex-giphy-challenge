<?php

declare(strict_types=1);

namespace Src\Shared\Domain;

final class InvalidUserId extends InvalidArgument
{
    public static function notPositive(int $value): self
    {
        return new self(sprintf('User id must be a positive integer, got %d.', $value));
    }
}
