<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

use Src\Shared\Domain\DomainException;

final class GifProviderUnavailable extends DomainException
{
    public function __construct()
    {
        parent::__construct('The GIF service is unavailable. Please try again later.');
    }
}
