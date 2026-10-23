<?php

declare(strict_types=1);

use Src\Favorites\Domain\Alias;
use Src\Favorites\Domain\FavoriteGif;
use Src\Gifs\Domain\GifId;
use Src\Shared\Domain\UserId;

test('favorite gif is created with user, gif and alias', function (): void {
    $favorite = FavoriteGif::create(new UserId(1), new GifId('abc123'), new Alias('Funny cat'));

    expect($favorite->id)->toBeNull()
        ->and($favorite->userId->value())->toBe(1)
        ->and($favorite->gifId->value())->toBe('abc123')
        ->and($favorite->alias->value())->toBe('Funny cat')
        ->and($favorite->createdAt)->toBeNull();
});
