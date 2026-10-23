<?php

declare(strict_types=1);

namespace Src\Favorites\Application\SaveFavoriteGif;

use Src\Favorites\Domain\Alias;
use Src\Favorites\Domain\FavoriteAlreadyExists;
use Src\Favorites\Domain\FavoriteGif;
use Src\Favorites\Domain\FavoriteGifRepository;
use Src\Favorites\Domain\InvalidAlias;
use Src\Gifs\Domain\GifCatalog;
use Src\Gifs\Domain\GifId;
use Src\Gifs\Domain\GifNotFound;
use Src\Gifs\Domain\GifProviderUnavailable;
use Src\Gifs\Domain\InvalidGifId;
use Src\Shared\Domain\InvalidUserId;
use Src\Shared\Domain\UserId;

final readonly class SaveFavoriteGifHandler
{
    public function __construct(
        private GifCatalog $catalog,
        private FavoriteGifRepository $favorites,
    ) {
    }

    /**
     * @throws InvalidUserId
     * @throws InvalidGifId
     * @throws InvalidAlias
     * @throws GifNotFound
     * @throws GifProviderUnavailable
     * @throws FavoriteAlreadyExists
     */
    public function handle(SaveFavoriteGifCommand $command): FavoriteGif
    {
        $userId = new UserId($command->userId);
        $gifId = new GifId($command->gifId);
        $alias = new Alias($command->alias);

        $this->catalog->find($gifId);

        if ($this->favorites->existsFor($userId, $gifId)) {
            throw new FavoriteAlreadyExists();
        }

        return $this->favorites->save(FavoriteGif::create($userId, $gifId, $alias));
    }
}
