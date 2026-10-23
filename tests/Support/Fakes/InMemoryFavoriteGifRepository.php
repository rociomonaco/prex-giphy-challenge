<?php

declare(strict_types=1);

namespace Tests\Support\Fakes;

use DateTimeImmutable;
use Src\Favorites\Domain\FavoriteAlreadyExists;
use Src\Favorites\Domain\FavoriteGif;
use Src\Favorites\Domain\FavoriteGifRepository;
use Src\Gifs\Domain\GifId;
use Src\Shared\Domain\UserId;

final class InMemoryFavoriteGifRepository implements FavoriteGifRepository
{
    /** @var list<FavoriteGif> */
    private array $favorites = [];

    private int $nextId = 1;

    public function __construct(
        private readonly DateTimeImmutable $now = new DateTimeImmutable(),
    ) {
    }

    public function existsFor(UserId $userId, GifId $gifId): bool
    {
        foreach ($this->favorites as $favorite) {
            if ($favorite->userId->equals($userId) && $favorite->gifId->equals($gifId)) {
                return true;
            }
        }

        return false;
    }

    public function save(FavoriteGif $favorite): FavoriteGif
    {
        if ($this->existsFor($favorite->userId, $favorite->gifId)) {
            throw new FavoriteAlreadyExists();
        }

        $saved = FavoriteGif::fromPersistence(
            $this->nextId,
            $favorite->userId,
            $favorite->gifId,
            $favorite->alias,
            $favorite->createdAt ?? $this->now,
        );

        $this->nextId++;
        $this->favorites[] = $saved;

        return $saved;
    }

    /**
     * @return list<FavoriteGif>
     */
    public function all(): array
    {
        return $this->favorites;
    }
}
