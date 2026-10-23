<?php

declare(strict_types=1);

namespace Src\Favorites\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Src\Favorites\Domain\FavoriteGifRepository;
use Src\Favorites\Infrastructure\Persistence\EloquentFavoriteGifRepository;

final class FavoritesServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        FavoriteGifRepository::class => EloquentFavoriteGifRepository::class,
    ];
}
