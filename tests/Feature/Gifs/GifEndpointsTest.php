<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Laravel\Passport\Passport;
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

const EXPECTED_GIF = [
    'id' => '774uK11mxE3W0cjE8L',
    'title' => 'High Five Ai GIF',
    'url' => 'https://giphy.com/gifs/ai-cat-meme-774uK11mxE3W0cjE8L',
    'embed_url' => 'https://giphy.com/embed/774uK11mxE3W0cjE8L',
    'rating' => 'g',
    'username' => 'enayatperak',
    'images' => [
        'original' => [
            'url' => 'https://media4.giphy.com/media/774uK11mxE3W0cjE8L/giphy.gif',
            'width' => 480,
            'height' => 480,
        ],
        'preview' => [
            'url' => 'https://media4.giphy.com/media/774uK11mxE3W0cjE8L/200w.gif',
            'width' => 200,
            'height' => 200,
        ],
    ],
];

test('search endpoint returns 200 with data and pagination', function (): void {
    Passport::actingAs(User::factory()->create());
    Http::fake(['api.giphy.com/v1/gifs/search*' => Http::response(GiphyFixture::load('search'))]);

    $this->getJson(route('gifs.search', ['query' => 'cat', 'limit' => 2]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0', EXPECTED_GIF)
        ->assertJsonPath('data.1.id', 'tphCApwvdtC1VJabZ1')
        ->assertJsonPath('pagination', ['total' => 500, 'count' => 2, 'offset' => 0]);
});

test('search endpoint returns 401 without a token', function (array $headers): void {
    $this->get(route('gifs.search', ['query' => 'cat']), $headers)
        ->assertUnauthorized()
        ->assertExactJson([
            'error' => [
                'code' => 'unauthenticated',
                'message' => 'Missing, invalid or expired token.',
            ],
        ]);

    Http::assertNothingSent();
})->with([
    'json client' => [['Accept' => 'application/json']],
    'client without accept header' => [[]],
]);

test('search endpoint returns 422 with invalid filters', function (array $params, string $field): void {
    Passport::actingAs(User::factory()->create());

    $this->getJson(route('gifs.search', $params))
        ->assertUnprocessable()
        ->assertJsonPath('error.code', 'validation_error')
        ->assertJsonValidationErrors($field, 'error.details');

    Http::assertNothingSent();
})->with([
    'missing query' => [[], 'query'],
    'limit 0' => [['query' => 'cat', 'limit' => 0], 'limit'],
    'limit 51' => [['query' => 'cat', 'limit' => 51], 'limit'],
    'offset -1' => [['query' => 'cat', 'offset' => -1], 'offset'],
    'limit not a number' => [['query' => 'cat', 'limit' => 'abc'], 'limit'],
]);

test('show endpoint returns 200 with the gif', function (): void {
    Passport::actingAs(User::factory()->create());
    Http::fake(['api.giphy.com/v1/gifs/774uK11mxE3W0cjE8L*' => Http::response(GiphyFixture::load('gif'))]);

    $this->getJson(route('gifs.show', ['id' => '774uK11mxE3W0cjE8L']))
        ->assertOk()
        ->assertExactJson(['data' => EXPECTED_GIF]);
});

test('show endpoint returns 404 when the gif does not exist', function (): void {
    Passport::actingAs(User::factory()->create());
    Http::fake(['api.giphy.com/v1/gifs/*' => Http::response(GiphyFixture::load('not_found'), 404)]);

    $this->getJson(route('gifs.show', ['id' => 'doesnotexist123']))
        ->assertNotFound()
        ->assertExactJson([
            'error' => [
                'code' => 'gif_not_found',
                'message' => 'GIF not found.',
            ],
        ]);
});

test('gif endpoints return 502 when giphy is unavailable', function (): void {
    Passport::actingAs(User::factory()->create());
    Http::fake(['api.giphy.com/*' => Http::response(['meta' => ['status' => 503]], 503)]);

    $expected = [
        'error' => [
            'code' => 'gif_provider_unavailable',
            'message' => 'The GIF service is unavailable. Please try again later.',
        ],
    ];

    $this->getJson(route('gifs.search', ['query' => 'cat']))
        ->assertStatus(502)
        ->assertExactJson($expected);

    $this->getJson(route('gifs.show', ['id' => '774uK11mxE3W0cjE8L']))
        ->assertStatus(502)
        ->assertExactJson($expected);
});
