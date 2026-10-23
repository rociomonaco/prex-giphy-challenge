<?php

declare(strict_types=1);

use Src\Gifs\Application\FindGif\FindGifHandler;
use Src\Gifs\Application\FindGif\FindGifQuery;
use Src\Gifs\Domain\GifNotFound;
use Tests\Support\Fakes\FakeGifCatalog;
use Tests\Support\GifMother;

beforeEach(function (): void {
    $this->gif = GifMother::create('abc123');
    $this->handler = new FindGifHandler(new FakeGifCatalog([$this->gif]));
});

test('find gif returns the requested gif', function (): void {
    expect($this->handler->handle(new FindGifQuery('abc123')))->toBe($this->gif);
});

test('find gif fails when the gif does not exist', function (): void {
    $this->handler->handle(new FindGifQuery('missing999'));
})->throws(GifNotFound::class, 'GIF not found.');
