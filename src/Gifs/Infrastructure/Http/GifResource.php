<?php

declare(strict_types=1);

namespace Src\Gifs\Infrastructure\Http;

use Src\Gifs\Domain\Gif;
use Src\Gifs\Domain\GifImage;

final class GifResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Gif $gif): array
    {
        return [
            'id' => $gif->id->value(),
            'title' => $gif->title,
            'url' => $gif->url,
            'embed_url' => $gif->embedUrl,
            'rating' => $gif->rating,
            'username' => $gif->username,
            'images' => [
                'original' => self::image($gif->original),
                'preview' => self::image($gif->preview),
            ],
        ];
    }

    /**
     * @return array{url: string, width: int, height: int}
     */
    private static function image(GifImage $image): array
    {
        return [
            'url' => $image->url,
            'width' => $image->width,
            'height' => $image->height,
        ];
    }
}
