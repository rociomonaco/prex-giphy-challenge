<?php

declare(strict_types=1);

namespace Src\Favorites\Application\SaveFavoriteGif;

final readonly class SaveFavoriteGifCommand
{
    public function __construct(
        public int $userId,
        public string $gifId,
        public string $alias,
    ) {
    }
}
