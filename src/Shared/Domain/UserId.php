<?php

declare(strict_types=1);

namespace Src\Shared\Domain;

final readonly class UserId
{
    public function __construct(private int $value)
    {
        if ($value <= 0) {
            throw InvalidUserId::notPositive($value);
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
