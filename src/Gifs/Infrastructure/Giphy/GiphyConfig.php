<?php

declare(strict_types=1);

namespace Src\Gifs\Infrastructure\Giphy;

final readonly class GiphyConfig
{
    public function __construct(
        public string $apiKey,
        public string $baseUrl,
        public float $timeout,
    ) {
    }
}
