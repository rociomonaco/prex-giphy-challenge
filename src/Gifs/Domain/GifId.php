<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

final readonly class GifId
{
    public const MAX_LENGTH = 64;

    public function __construct(private string $value)
    {
        if (strlen($value) > self::MAX_LENGTH || preg_match('/^[A-Za-z0-9]+$/', $value) !== 1) {
            throw new InvalidGifId();
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
