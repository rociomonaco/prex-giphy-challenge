<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

interface GifCatalog
{
    /**
     * @throws GifProviderUnavailable
     */
    public function search(SearchCriteria $criteria): GifCollection;

    /**
     * @throws GifNotFound
     * @throws GifProviderUnavailable
     */
    public function find(GifId $id): Gif;
}
