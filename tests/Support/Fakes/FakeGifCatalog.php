<?php

declare(strict_types=1);

namespace Tests\Support\Fakes;

use Src\Gifs\Domain\Gif;
use Src\Gifs\Domain\GifCatalog;
use Src\Gifs\Domain\GifCollection;
use Src\Gifs\Domain\GifId;
use Src\Gifs\Domain\GifNotFound;
use Src\Gifs\Domain\GifProviderUnavailable;
use Src\Gifs\Domain\Pagination;
use Src\Gifs\Domain\SearchCriteria;

final class FakeGifCatalog implements GifCatalog
{
    private bool $unavailable = false;

    private ?SearchCriteria $lastCriteria = null;

    /**
     * @param list<Gif> $gifs
     */
    public function __construct(private readonly array $gifs = [])
    {
    }

    public function makeUnavailable(): self
    {
        $this->unavailable = true;

        return $this;
    }

    public function search(SearchCriteria $criteria): GifCollection
    {
        $this->guardAvailability();
        $this->lastCriteria = $criteria;

        $page = array_slice($this->gifs, $criteria->offset(), $criteria->limit());

        return new GifCollection($page, new Pagination(count($this->gifs), count($page), $criteria->offset()));
    }

    public function find(GifId $id): Gif
    {
        $this->guardAvailability();

        foreach ($this->gifs as $gif) {
            if ($gif->id->equals($id)) {
                return $gif;
            }
        }

        throw new GifNotFound();
    }

    public function lastCriteria(): ?SearchCriteria
    {
        return $this->lastCriteria;
    }

    private function guardAvailability(): void
    {
        if ($this->unavailable) {
            throw new GifProviderUnavailable();
        }
    }
}
