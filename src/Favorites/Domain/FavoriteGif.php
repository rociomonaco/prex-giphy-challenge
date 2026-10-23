<?php

declare(strict_types=1);

namespace Src\Favorites\Domain;

use DateTimeImmutable;
use Src\Gifs\Domain\GifId;
use Src\Shared\Domain\UserId;

final readonly class FavoriteGif
{
    private function __construct(
        public ?int $id,
        public UserId $userId,
        public GifId $gifId,
        public Alias $alias,
        public ?DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(UserId $userId, GifId $gifId, Alias $alias): self
    {
        return new self(null, $userId, $gifId, $alias, null);
    }

    public static function fromPersistence(
        int $id,
        UserId $userId,
        GifId $gifId,
        Alias $alias,
        DateTimeImmutable $createdAt,
    ): self {
        return new self($id, $userId, $gifId, $alias, $createdAt);
    }
}
