<?php

declare(strict_types=1);

namespace Src\Favorites\Domain;

use Src\Gifs\Domain\GifId;
use Src\Shared\Domain\UserId;

interface FavoriteGifRepository
{
    public function existsFor(UserId $userId, GifId $gifId): bool;

    /**
     * @throws FavoriteAlreadyExists
     */
    public function save(FavoriteGif $favorite): FavoriteGif;
}
