<?php

declare(strict_types=1);

namespace Src\Favorites\Domain;

use Src\Shared\Domain\DomainException;

final class FavoriteAlreadyExists extends DomainException
{
    public function __construct()
    {
        parent::__construct('This GIF is already in your favorites.');
    }
}
