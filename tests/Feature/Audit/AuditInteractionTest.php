<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Laravel\Passport\Passport;
use Src\Audit\Infrastructure\Persistence\AuditLogModel;
use Tests\Support\GiphyFixture;

test('login is audited without password nor token', function (): void {
    $this->createPersonalAccessClient();
    $user = User::factory()->create(['email' => 'test@prex.com', 'password' => 'password']);

    $response = $this->postJson(route('auth.login'), [
        'email' => 'test@prex.com',
        'password' => 'password',
    ])->assertOk();

    expect($response->json())->not->toHaveKeys(['user_id', 'userId']);

    $log = AuditLogModel::query()->sole();
    $responseBody = json_decode($log->response_body, true);

    expect($log->user_id)->toBe($user->id)
        ->and($log->service)->toBe('auth.login')
        ->and($log->method)->toBe('POST')
        ->and($log->path)->toBe('/api/v1/auth/login')
        ->and($log->response_status)->toBe(200)
        ->and($log->ip_address)->toBe('127.0.0.1')
        ->and($log->request_body)->toEqual([
            'email' => 'test@prex.com',
            'password' => '***',
        ])
        ->and($responseBody['token_type'])->toBe('Bearer')
        ->and($responseBody['access_token'])->toBe('***')
        ->and($log->response_body)->not->toContain($response->json('access_token'));
});

test('failed login is audited without user', function (): void {
    $this->createPersonalAccessClient();
    User::factory()->create(['email' => 'test@prex.com', 'password' => 'password']);

    $this->postJson(route('auth.login'), [
        'email' => 'test@prex.com',
        'password' => 'wrong-password',
    ])->assertUnauthorized();

    $log = AuditLogModel::query()->sole();

    expect($log->user_id)->toBeNull()
        ->and($log->service)->toBe('auth.login')
        ->and($log->response_status)->toBe(401)
        ->and($log->request_body['email'])->toBe('test@prex.com')
        ->and($log->request_body['password'])->toBe('***')
        ->and($log->response_body)->not->toContain('wrong-password');
});

test('gif search is audited with user, service, status and ip', function (): void {
    config()->set('services.giphy', [
        'key' => 'test-key',
        'base_url' => 'https://api.giphy.com/v1',
        'timeout' => 5,
    ]);
    Http::preventStrayRequests();
    Sleep::fake();
    Http::fake(['api.giphy.com/v1/gifs/search*' => Http::response(GiphyFixture::load('search'))]);

    $user = User::factory()->create();
    Passport::actingAs($user);

    $this->getJson(route('gifs.search', ['query' => 'cat', 'limit' => 2]))->assertOk();

    $log = AuditLogModel::query()->sole();

    expect($log->user_id)->toBe($user->id)
        ->and($log->service)->toBe('gifs.search')
        ->and($log->method)->toBe('GET')
        ->and($log->response_status)->toBe(200)
        ->and($log->ip_address)->toBe('127.0.0.1')
        ->and($log->request_body['query'])->toBe('cat');
});

test('unauthenticated requests are audited', function (): void {
    config()->set('services.giphy', [
        'key' => 'test-key',
        'base_url' => 'https://api.giphy.com/v1',
        'timeout' => 5,
    ]);
    Http::preventStrayRequests();

    $this->getJson(route('gifs.search', ['query' => 'cat']))->assertUnauthorized();

    $log = AuditLogModel::query()->sole();

    expect($log->user_id)->toBeNull()
        ->and($log->service)->toBe('gifs.search')
        ->and($log->response_status)->toBe(401)
        ->and($log->ip_address)->toBe('127.0.0.1');

    Http::assertNothingSent();
});

test('saving a favorite is audited with request and response bodies', function (): void {
    config()->set('services.giphy', [
        'key' => 'test-key',
        'base_url' => 'https://api.giphy.com/v1',
        'timeout' => 5,
    ]);
    Http::preventStrayRequests();
    Sleep::fake();
    Http::fake(['api.giphy.com/v1/gifs/774uK11mxE3W0cjE8L*' => Http::response(GiphyFixture::load('gif'))]);

    $user = User::factory()->create();
    Passport::actingAs($user);

    $this->postJson(route('favorites.store'), [
        'gif_id' => '774uK11mxE3W0cjE8L',
        'alias' => 'Funny cat',
        'user_id' => $user->id,
    ])->assertCreated();

    $log = AuditLogModel::query()->sole();
    $responseBody = json_decode($log->response_body, true);

    expect($log->user_id)->toBe($user->id)
        ->and($log->service)->toBe('favorites.store')
        ->and($log->response_status)->toBe(201)
        ->and($log->request_body)->toEqual([
            'gif_id' => '774uK11mxE3W0cjE8L',
            'alias' => 'Funny cat',
            'user_id' => $user->id,
        ])
        ->and($responseBody['data']['gif_id'])->toBe('774uK11mxE3W0cjE8L')
        ->and($responseBody['data']['alias'])->toBe('Funny cat')
        ->and($responseBody['data']['user_id'])->toBe($user->id);
});
