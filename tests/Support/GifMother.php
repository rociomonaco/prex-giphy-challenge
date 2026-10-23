<?php

declare(strict_types=1);

namespace Tests\Support;

use Src\Gifs\Domain\Gif;
use Src\Gifs\Domain\GifId;
use Src\Gifs\Domain\GifImage;

final class GifMother
{
    public static function create(string $id = 'abc123'): Gif
    {
        return new Gif(
            id: new GifId($id),
            title: "GIF {$id}",
            url: "https://giphy.com/gifs/{$id}",
            embedUrl: "https://giphy.com/embed/{$id}",
            rating: 'g',
            username: 'prex',
            original: new GifImage("https://media.giphy.com/media/{$id}/giphy.gif", 480, 270),
            preview: new GifImage("https://media.giphy.com/media/{$id}/200w.gif", 200, 113),
        );
    }
}
