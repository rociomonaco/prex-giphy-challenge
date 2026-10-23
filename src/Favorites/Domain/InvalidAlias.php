<?php

declare(strict_types=1);

namespace Src\Favorites\Domain;

use Src\Shared\Domain\InvalidArgument;

final class InvalidAlias extends InvalidArgument
{
    public function __construct()
    {
        parent::__construct(sprintf(
            'Alias must be a non-empty string of at most %d characters.',
            Alias::MAX_LENGTH,
        ));
    }
}
