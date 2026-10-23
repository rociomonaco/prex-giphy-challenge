<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

final readonly class GifImage
{
    public function __construct(
        public string $url,
        public int $width,
        public int $height,
    ) {
    }
}
