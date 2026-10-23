<?php

declare(strict_types=1);

namespace Src\Favorites\Domain;

final readonly class Alias
{
    public const MAX_LENGTH = 255;

    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        $length = mb_strlen($trimmed);

        if ($length === 0 || $length > self::MAX_LENGTH) {
            throw new InvalidAlias();
        }

        $this->value = $trimmed;
    }

    public function value(): string
    {
        return $this->value;
    }
}
