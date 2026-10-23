<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

final readonly class GifCollection
{
    /**
     * @param list<Gif> $gifs
     */
    public function __construct(
        public array $gifs,
        public Pagination $pagination,
    ) {
    }
}
