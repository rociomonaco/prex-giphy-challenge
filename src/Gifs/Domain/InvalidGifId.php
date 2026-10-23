<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

use Src\Shared\Domain\InvalidArgument;

final class InvalidGifId extends InvalidArgument
{
    public function __construct()
    {
        parent::__construct(sprintf(
            'GIF id must be a non-empty alphanumeric string of at most %d characters.',
            GifId::MAX_LENGTH,
        ));
    }
}
