<?php

declare(strict_types=1);

use App\Models\User;
use DateTimeInterface;
use Src\Favorites\Domain\Alias;
use Src\Favorites\Domain\FavoriteAlreadyExists;
use Src\Favorites\Domain\FavoriteGif;
use Src\Favorites\Domain\FavoriteGifRepository;
use Src\Favorites\Infrastructure\Persistence\FavoriteGifModel;
use Src\Gifs\Domain\GifId;
use Src\Shared\Domain\UserId;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->repository = $this->app->make(FavoriteGifRepository::class);
});

test('eloquent repository saves and maps a favorite', function (): void {
    $saved = $this->repository->save(FavoriteGif::create(
        new UserId($this->user->id),
        new GifId('abc123'),
        new Alias('  Funny cat  '),
    ));

    $stored = FavoriteGifModel::query()->findOrFail($saved->id);

    expect($saved->userId->value())->toBe($this->user->id)
        ->and($saved->gifId->value())->toBe('abc123')
        ->and($saved->alias->value())->toBe('Funny cat')
        ->and($saved->id)->toBe($stored->id)
        ->and($saved->userId->value())->toBe($stored->user_id)
        ->and($saved->gifId->value())->toBe($stored->gif_id)
        ->and($saved->alias->value())->toBe($stored->alias)
        ->and($saved->createdAt?->format(DateTimeInterface::ATOM))
        ->toBe($stored->created_at->toDateTimeImmutable()->format(DateTimeInterface::ATOM));
});

test('eloquent repository detects an existing favorite', function (): void {
    $userId = new UserId($this->user->id);
    $gifId = new GifId('abc123');

    expect($this->repository->existsFor($userId, $gifId))->toBeFalse();

    $this->repository->save(FavoriteGif::create($userId, $gifId, new Alias('Funny cat')));

    expect($this->repository->existsFor($userId, $gifId))->toBeTrue();
});

test('database rejects duplicated favorites', function (): void {
    $userId = new UserId($this->user->id);
    $gifId = new GifId('abc123');

    $this->repository->save(FavoriteGif::create($userId, $gifId, new Alias('Funny cat')));

    expect(fn () => $this->repository->save(FavoriteGif::create($userId, $gifId, new Alias('Another alias'))))
        ->toThrow(FavoriteAlreadyExists::class, 'This GIF is already in your favorites.');
});
