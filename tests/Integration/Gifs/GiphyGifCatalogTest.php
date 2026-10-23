<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Src\Gifs\Domain\GifCatalog;
use Src\Gifs\Domain\GifId;
use Src\Gifs\Domain\GifNotFound;
use Src\Gifs\Domain\GifProviderUnavailable;
use Src\Gifs\Domain\SearchCriteria;
use Tests\Support\GiphyFixture;

beforeEach(function (): void {
    config()->set('services.giphy', [
        'key' => 'test-key',
        'base_url' => 'https://api.giphy.com/v1',
        'timeout' => 5,
    ]);

    Http::preventStrayRequests();
    Sleep::fake();
});

test('giphy catalog maps search results and pagination', function (): void {
    Http::fake(['api.giphy.com/v1/gifs/search*' => Http::response(GiphyFixture::load('search'))]);

    $result = app(GifCatalog::class)->search(new SearchCriteria('cat', limit: 2));

    expect($result->gifs)->toHaveCount(2)
        ->and($result->gifs[0]->id->value())->toBe('774uK11mxE3W0cjE8L')
        ->and($result->gifs[1]->id->value())->toBe('tphCApwvdtC1VJabZ1')
        ->and($result->pagination->total)->toBe(500)
        ->and($result->pagination->count)->toBe(2)
        ->and($result->pagination->offset)->toBe(0);
});

test('giphy catalog maps a single gif', function (): void {
    Http::fake(['api.giphy.com/v1/gifs/774uK11mxE3W0cjE8L*' => Http::response(GiphyFixture::load('gif'))]);

    $gif = app(GifCatalog::class)->find(new GifId('774uK11mxE3W0cjE8L'));

    expect($gif->id->value())->toBe('774uK11mxE3W0cjE8L')
        ->and($gif->title)->toBe('High Five Ai GIF')
        ->and($gif->url)->toBe('https://giphy.com/gifs/ai-cat-meme-774uK11mxE3W0cjE8L')
        ->and($gif->embedUrl)->toBe('https://giphy.com/embed/774uK11mxE3W0cjE8L')
        ->and($gif->rating)->toBe('g')
        ->and($gif->username)->toBe('enayatperak')
        ->and($gif->original->url)->toBe('https://media4.giphy.com/media/774uK11mxE3W0cjE8L/giphy.gif')
        ->and($gif->original->width)->toBe(480)
        ->and($gif->original->height)->toBe(480)
        ->and($gif->preview->url)->toBe('https://media4.giphy.com/media/774uK11mxE3W0cjE8L/200w.gif')
        ->and($gif->preview->width)->toBe(200)
        ->and($gif->preview->height)->toBe(200);
});

test('giphy catalog sends api key, query, limit and offset', function (): void {
    Http::fake(['api.giphy.com/v1/gifs/search*' => Http::response(GiphyFixture::load('search'))]);

    app(GifCatalog::class)->search(new SearchCriteria('funny cats', limit: 10, offset: 20));

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://api.giphy.com/v1/gifs/search?')
        && $request['api_key'] === 'test-key'
        && $request['q'] === 'funny cats'
        && (int) $request['limit'] === 10
        && (int) $request['offset'] === 20);
});

test('giphy catalog throws not found when giphy returns 404', function (string $fixture, int $status): void {
    Http::fake(['api.giphy.com/v1/gifs/*' => Http::response(GiphyFixture::load($fixture), $status)]);

    expect(fn () => app(GifCatalog::class)->find(new GifId('doesnotexist123')))
        ->toThrow(GifNotFound::class, 'GIF not found.');

    Http::assertSentCount(1);
})->with([
    'unknown id (404)' => ['not_found', 404],
    'id rejected by giphy (400)' => ['invalid_id', 400],
]);

test('giphy catalog throws provider unavailable on server error', function (): void {
    Http::fake(['api.giphy.com/v1/gifs/search*' => Http::response(['meta' => ['status' => 500]], 500)]);

    expect(fn () => app(GifCatalog::class)->search(new SearchCriteria('cat')))
        ->toThrow(GifProviderUnavailable::class, 'The GIF service is unavailable. Please try again later.');

    Http::assertSentCount(2);
});

test('giphy catalog throws provider unavailable on connection error', function (): void {
    Http::fake(['api.giphy.com/v1/gifs/*' => Http::failedConnection()]);

    expect(fn () => app(GifCatalog::class)->find(new GifId('774uK11mxE3W0cjE8L')))
        ->toThrow(GifProviderUnavailable::class, 'The GIF service is unavailable. Please try again later.');

    Http::assertSentCount(2);
});
