<?php

declare(strict_types=1);

use Src\Favorites\Application\SaveFavoriteGif\SaveFavoriteGifCommand;
use Src\Favorites\Application\SaveFavoriteGif\SaveFavoriteGifHandler;
use Src\Favorites\Domain\FavoriteAlreadyExists;
use Src\Gifs\Domain\GifNotFound;
use Tests\Support\Fakes\FakeGifCatalog;
use Tests\Support\Fakes\InMemoryFavoriteGifRepository;
use Tests\Support\GifMother;

beforeEach(function (): void {
    $this->now = new DateTimeImmutable('2026-01-01 12:00:00');
    $this->gif = GifMother::create('abc123');
    $this->favorites = new InMemoryFavoriteGifRepository($this->now);
    $this->handler = new SaveFavoriteGifHandler(new FakeGifCatalog([$this->gif]), $this->favorites);
});

test('saves a favorite when the gif exists', function (): void {
    $saved = $this->handler->handle(new SaveFavoriteGifCommand(1, 'abc123', 'Funny cat'));

    expect($saved->id)->toBe(1)
        ->and($saved->userId->value())->toBe(1)
        ->and($saved->gifId->value())->toBe('abc123')
        ->and($saved->alias->value())->toBe('Funny cat')
        ->and($saved->createdAt)->toEqual($this->now)
        ->and($this->favorites->all())->toBe([$saved]);
});

test('fails when the gif does not exist', function (): void {
    try {
        $this->handler->handle(new SaveFavoriteGifCommand(1, 'missing999', 'Funny cat'));
    } catch (GifNotFound $exception) {
        expect($exception->getMessage())->toBe('GIF not found.')
            ->and($this->favorites->all())->toBeEmpty();

        return;
    }

    throw new Exception('Expected GifNotFound to be thrown.');
});

test('fails when the favorite already exists', function (): void {
    $this->handler->handle(new SaveFavoriteGifCommand(1, 'abc123', 'Funny cat'));

    $this->handler->handle(new SaveFavoriteGifCommand(1, 'abc123', 'Another alias'));
})->throws(FavoriteAlreadyExists::class, 'This GIF is already in your favorites.');

test('the same gif can be a favorite of different users', function (): void {
    $first = $this->handler->handle(new SaveFavoriteGifCommand(1, 'abc123', 'Mine'));
    $second = $this->handler->handle(new SaveFavoriteGifCommand(2, 'abc123', 'Theirs'));

    expect($first->userId->value())->toBe(1)
        ->and($second->userId->value())->toBe(2)
        ->and($this->favorites->all())->toBe([$first, $second]);
});
