<?php

declare(strict_types=1);

use Src\Gifs\Application\SearchGifs\SearchGifsHandler;
use Src\Gifs\Application\SearchGifs\SearchGifsQuery;
use Tests\Support\Fakes\FakeGifCatalog;
use Tests\Support\GifMother;

test('search gifs returns the catalog results', function (): void {
    $gifs = [GifMother::create('aaa1'), GifMother::create('bbb2'), GifMother::create('ccc3')];
    $catalog = new FakeGifCatalog($gifs);

    $result = (new SearchGifsHandler($catalog))->handle(new SearchGifsQuery(' cats ', limit: 2, offset: 1));

    expect($result->gifs)->toBe([$gifs[1], $gifs[2]])
        ->and($result->pagination->total)->toBe(3)
        ->and($result->pagination->count)->toBe(2)
        ->and($result->pagination->offset)->toBe(1)
        ->and($catalog->lastCriteria()?->query())->toBe('cats');
});
