<?php

declare(strict_types=1);

namespace Src\Gifs\Application\FindGif;

use Src\Gifs\Domain\Gif;
use Src\Gifs\Domain\GifCatalog;
use Src\Gifs\Domain\GifId;
use Src\Gifs\Domain\GifNotFound;
use Src\Gifs\Domain\GifProviderUnavailable;
use Src\Gifs\Domain\InvalidGifId;

final readonly class FindGifHandler
{
    public function __construct(private GifCatalog $catalog)
    {
    }

    /**
     * @throws InvalidGifId
     * @throws GifNotFound
     * @throws GifProviderUnavailable
     */
    public function handle(FindGifQuery $query): Gif
    {
        return $this->catalog->find(new GifId($query->id));
    }
}
