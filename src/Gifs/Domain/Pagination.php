<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

final readonly class Pagination
{
    public function __construct(
        public int $total,
        public int $count,
        public int $offset,
    ) {
    }
}
