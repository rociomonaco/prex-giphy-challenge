<?php

declare(strict_types=1);

namespace Src\Gifs\Application\SearchGifs;

final readonly class SearchGifsQuery
{
    public function __construct(
        public string $query,
        public ?int $limit = null,
        public ?int $offset = null,
    ) {
    }
}
