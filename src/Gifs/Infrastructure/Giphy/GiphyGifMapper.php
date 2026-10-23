<?php

declare(strict_types=1);

namespace Src\Gifs\Infrastructure\Giphy;

use Src\Gifs\Domain\Gif;
use Src\Gifs\Domain\GifCollection;
use Src\Gifs\Domain\GifId;
use Src\Gifs\Domain\GifImage;
use Src\Gifs\Domain\InvalidGifId;
use Src\Gifs\Domain\Pagination;
use UnexpectedValueException;

final class GiphyGifMapper
{
    /**
     * @throws UnexpectedValueException
     */
    public function toCollection(mixed $payload): GifCollection
    {
        $data = is_array($payload) ? ($payload['data'] ?? null) : null;
        $pagination = is_array($payload) ? ($payload['pagination'] ?? null) : null;

        if (! is_array($data) || ! array_is_list($data) || ! is_array($pagination)) {
            throw new UnexpectedValueException('Malformed GIPHY search payload.');
        }

        return new GifCollection(
            array_map(fn (mixed $item): Gif => $this->toGif($item), $data),
            new Pagination(
                total: $this->int($pagination, 'total_count'),
                count: $this->int($pagination, 'count'),
                offset: $this->int($pagination, 'offset'),
            ),
        );
    }

    /**
     * @throws UnexpectedValueException
     */
    public function toGif(mixed $item): Gif
    {
        if (! is_array($item) || ! is_array($item['images'] ?? null)) {
            throw new UnexpectedValueException('Malformed GIPHY gif payload.');
        }

        $original = $this->image($item['images']['original'] ?? null);

        return new Gif(
            id: $this->gifId($item),
            title: $this->string($item, 'title'),
            url: $this->requiredString($item, 'url'),
            embedUrl: $this->string($item, 'embed_url'),
            rating: $this->string($item, 'rating'),
            username: $this->string($item, 'username'),
            original: $original,
            preview: isset($item['images']['fixed_width'])
                ? $this->image($item['images']['fixed_width'])
                : $original,
        );
    }

    /**
     * @param array<array-key, mixed> $item
     */
    private function gifId(array $item): GifId
    {
        try {
            return new GifId($this->requiredString($item, 'id'));
        } catch (InvalidGifId) {
            throw new UnexpectedValueException('Malformed GIPHY gif id.');
        }
    }

    private function image(mixed $image): GifImage
    {
        if (! is_array($image)) {
            throw new UnexpectedValueException('Malformed GIPHY image payload.');
        }

        return new GifImage(
            url: $this->requiredString($image, 'url'),
            width: $this->int($image, 'width'),
            height: $this->int($image, 'height'),
        );
    }

    /**
     * @param array<array-key, mixed> $source
     */
    private function requiredString(array $source, string $key): string
    {
        $value = $this->string($source, $key);

        if ($value === '') {
            throw new UnexpectedValueException(sprintf('Missing GIPHY field "%s".', $key));
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $source
     */
    private function string(array $source, string $key): string
    {
        return is_string($source[$key] ?? null) ? $source[$key] : '';
    }

    /**
     * @param array<array-key, mixed> $source
     */
    private function int(array $source, string $key): int
    {
        return is_numeric($source[$key] ?? null) ? (int) $source[$key] : 0;
    }
}
