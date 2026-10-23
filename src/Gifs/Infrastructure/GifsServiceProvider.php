<?php

declare(strict_types=1);

namespace Src\Gifs\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Src\Gifs\Domain\GifCatalog;
use Src\Gifs\Infrastructure\Giphy\GiphyConfig;
use Src\Gifs\Infrastructure\Giphy\GiphyGifCatalog;

final class GifsServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        GifCatalog::class => GiphyGifCatalog::class,
    ];

    public function register(): void
    {
        $this->app->bind(GiphyConfig::class, fn (): GiphyConfig => new GiphyConfig(
            apiKey: (string) config('services.giphy.key'),
            baseUrl: (string) config('services.giphy.base_url'),
            timeout: (float) config('services.giphy.timeout'),
        ));
    }
}
