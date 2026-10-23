<?php

declare(strict_types=1);

namespace Src\Gifs\Infrastructure\Http;

use Illuminate\Http\JsonResponse;
use Src\Gifs\Application\FindGif\FindGifHandler;
use Src\Gifs\Application\FindGif\FindGifQuery;

final readonly class FindGifController
{
    public function __construct(private FindGifHandler $handler)
    {
    }

    public function __invoke(string $id): JsonResponse
    {
        $gif = $this->handler->handle(new FindGifQuery($id));

        return new JsonResponse(['data' => GifResource::toArray($gif)]);
    }
}
