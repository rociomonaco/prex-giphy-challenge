<?php

declare(strict_types=1);

namespace Src\Favorites\Infrastructure\Http;

use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Src\Favorites\Application\SaveFavoriteGif\SaveFavoriteGifCommand;
use Src\Favorites\Application\SaveFavoriteGif\SaveFavoriteGifHandler;

final readonly class SaveFavoriteGifController
{
    public function __construct(private SaveFavoriteGifHandler $handler)
    {
    }

    public function __invoke(SaveFavoriteGifRequest $request): JsonResponse
    {
        $favorite = $this->handler->handle(new SaveFavoriteGifCommand(
            userId: $request->integer('user_id'),
            gifId: $request->string('gif_id')->toString(),
            alias: $request->string('alias')->toString(),
        ));

        return new JsonResponse([
            'data' => [
                'id' => $favorite->id,
                'user_id' => $favorite->userId->value(),
                'gif_id' => $favorite->gifId->value(),
                'alias' => $favorite->alias->value(),
                'created_at' => $favorite->createdAt?->format(DateTimeInterface::ATOM),
            ],
        ], 201);
    }
}
