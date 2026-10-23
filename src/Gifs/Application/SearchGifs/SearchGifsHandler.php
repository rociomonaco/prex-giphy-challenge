<?php

declare(strict_types=1);

namespace Src\Gifs\Application\SearchGifs;

use Src\Gifs\Domain\GifCatalog;
use Src\Gifs\Domain\GifCollection;
use Src\Gifs\Domain\GifProviderUnavailable;
use Src\Gifs\Domain\InvalidSearchCriteria;
use Src\Gifs\Domain\SearchCriteria;

final readonly class SearchGifsHandler
{
    public function __construct(private GifCatalog $catalog)
    {
    }

    /**
     * @throws InvalidSearchCriteria
     * @throws GifProviderUnavailable
     */
    public function handle(SearchGifsQuery $query): GifCollection
    {
        return $this->catalog->search(new SearchCriteria(
            $query->query,
            $query->limit ?? SearchCriteria::DEFAULT_LIMIT,
            $query->offset ?? SearchCriteria::DEFAULT_OFFSET,
        ));
    }
}
