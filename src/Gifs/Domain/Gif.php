<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

final readonly class Gif
{
    public function __construct(
        public GifId $id,
        public string $title,
        public string $url,
        public string $embedUrl,
        public string $rating,
        public string $username,
        public GifImage $original,
        public GifImage $preview,
    ) {
    }
}
