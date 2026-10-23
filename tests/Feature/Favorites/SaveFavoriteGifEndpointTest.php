<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Laravel\Passport\Passport;
use Src\Favorites\Infrastructure\Http\SaveFavoriteGifRequest;
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

test('save favorite returns 201', function (): void {
    $user = User::factory()->create();
    Passport::actingAs($user);
    Http::fake(['api.giphy.com/v1/gifs/774uK11mxE3W0cjE8L*' => Http::response(GiphyFixture::load('gif'))]);

    $response = $this->postJson(route('favorites.store'), [
        'gif_id' => '774uK11mxE3W0cjE8L',
        'alias' => 'Funny cat',
        'user_id' => $user->id,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.gif_id', '774uK11mxE3W0cjE8L')
        ->assertJsonPath('data.alias', 'Funny cat')
        ->assertJsonStructure(['data' => ['id', 'user_id', 'gif_id', 'alias', 'created_at']]);

    expect(DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $response->json('data.created_at')))
        ->toBeInstanceOf(DateTimeImmutable::class);

    $this->assertDatabaseHas('favorite_gifs', [
        'id' => $response->json('data.id'),
        'user_id' => $user->id,
        'gif_id' => '774uK11mxE3W0cjE8L',
        'alias' => 'Funny cat',
    ]);
});

test('save favorite returns 401 without a token', function (): void {
    $this->postJson(route('favorites.store'), [
        'gif_id' => '774uK11mxE3W0cjE8L',
        'alias' => 'Funny cat',
        'user_id' => 1,
    ])
        ->assertUnauthorized()
        ->assertExactJson([
            'error' => [
                'code' => 'unauthenticated',
                'message' => 'Missing, invalid or expired token.',
            ],
        ]);

    Http::assertNothingSent();
});

test('save favorite returns 403 for another user', function (): void {
    Passport::actingAs(User::factory()->create());
    $other = User::factory()->create();

    $this->postJson(route('favorites.store'), [
        'gif_id' => '774uK11mxE3W0cjE8L',
        'alias' => 'Funny cat',
        'user_id' => $other->id,
    ])
        ->assertForbidden()
        ->assertExactJson([
            'error' => [
                'code' => 'forbidden',
                'message' => SaveFavoriteGifRequest::FORBIDDEN_MESSAGE,
            ],
        ]);

    Http::assertNothingSent();
});

test('save favorite returns 404 when the gif does not exist', function (): void {
    $user = User::factory()->create();
    Passport::actingAs($user);
    Http::fake(['api.giphy.com/v1/gifs/*' => Http::response(GiphyFixture::load('not_found'), 404)]);

    $this->postJson(route('favorites.store'), [
        'gif_id' => 'doesnotexist123',
        'alias' => 'Funny cat',
        'user_id' => $user->id,
    ])
        ->assertNotFound()
        ->assertExactJson([
            'error' => [
                'code' => 'gif_not_found',
                'message' => 'GIF not found.',
            ],
        ]);

    $this->assertDatabaseCount('favorite_gifs', 0);
});

test('save favorite returns 409 when it already exists', function (): void {
    $user = User::factory()->create();
    Passport::actingAs($user);
    Http::fake(['api.giphy.com/v1/gifs/774uK11mxE3W0cjE8L*' => Http::response(GiphyFixture::load('gif'))]);

    $payload = [
        'gif_id' => '774uK11mxE3W0cjE8L',
        'alias' => 'Funny cat',
        'user_id' => $user->id,
    ];

    $this->postJson(route('favorites.store'), $payload)->assertCreated();

    $this->postJson(route('favorites.store'), [
        ...$payload,
        'alias' => 'Another alias',
    ])
        ->assertStatus(409)
        ->assertExactJson([
            'error' => [
                'code' => 'favorite_already_exists',
                'message' => 'This GIF is already in your favorites.',
            ],
        ]);
});

test('save favorite returns 422 with invalid input', function (string $field, array $payload): void {
    $user = User::factory()->create();
    Passport::actingAs($user);

    if ($field !== 'user_id') {
        $payload['user_id'] = $user->id;
    }

    $this->postJson(route('favorites.store'), $payload)
        ->assertUnprocessable()
        ->assertJsonPath('error.code', 'validation_error')
        ->assertJsonPath('error.message', 'The given data is invalid.')
        ->assertJsonValidationErrors($field, 'error.details');

    Http::assertNothingSent();
})->with([
    'missing gif_id' => ['gif_id', ['alias' => 'Funny cat']],
    'missing alias' => ['alias', ['gif_id' => 'abc123']],
    'missing user_id' => ['user_id', ['gif_id' => 'abc123', 'alias' => 'Funny cat']],
    'gif_id with symbols' => ['gif_id', ['gif_id' => 'abc-123', 'alias' => 'Funny cat']],
]);
