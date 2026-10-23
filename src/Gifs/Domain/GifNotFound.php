<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

use Src\Shared\Domain\DomainException;

final class GifNotFound extends DomainException
{
    public function __construct()
    {
        parent::__construct('GIF not found.');
    }
}
