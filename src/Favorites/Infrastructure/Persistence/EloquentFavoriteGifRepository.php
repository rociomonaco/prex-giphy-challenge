<?php

declare(strict_types=1);

namespace Src\Favorites\Infrastructure\Persistence;

use Illuminate\Database\UniqueConstraintViolationException;
use Src\Favorites\Domain\Alias;
use Src\Favorites\Domain\FavoriteAlreadyExists;
use Src\Favorites\Domain\FavoriteGif;
use Src\Favorites\Domain\FavoriteGifRepository;
use Src\Gifs\Domain\GifId;
use Src\Shared\Domain\UserId;

final class EloquentFavoriteGifRepository implements FavoriteGifRepository
{
    public function existsFor(UserId $userId, GifId $gifId): bool
    {
        return FavoriteGifModel::query()
            ->where('user_id', $userId->value())
            ->where('gif_id', $gifId->value())
            ->exists();
    }

    public function save(FavoriteGif $favorite): FavoriteGif
    {
        try {
            $model = FavoriteGifModel::query()->create([
                'user_id' => $favorite->userId->value(),
                'gif_id' => $favorite->gifId->value(),
                'alias' => $favorite->alias->value(),
            ]);
        } catch (UniqueConstraintViolationException) {
            throw new FavoriteAlreadyExists();
        }

        return $this->map($model);
    }

    private function map(FavoriteGifModel $model): FavoriteGif
    {
        return FavoriteGif::fromPersistence(
            id: (int) $model->getKey(),
            userId: new UserId((int) $model->user_id),
            gifId: new GifId((string) $model->gif_id),
            alias: new Alias((string) $model->alias),
            createdAt: $model->created_at->toDateTimeImmutable(),
        );
    }
}
