<?php

declare(strict_types=1);

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

test('unknown api routes return 404 with the error format', function (): void {
    $this->getJson('/api/v1/no-such-route')
        ->assertNotFound()
        ->assertExactJson([
            'error' => [
                'code' => 'not_found',
                'message' => 'Resource not found.',
            ],
        ]);

    $this->getJson('/api/v1/gifs/not-valid')
        ->assertNotFound()
        ->assertExactJson([
            'error' => [
                'code' => 'not_found',
                'message' => 'Resource not found.',
            ],
        ]);
});

test('unexpected errors return 500 without internal details', function (): void {
    config(['app.debug' => true]);

    $logged = false;

    Event::listen(MessageLogged::class, function (MessageLogged $event) use (&$logged): void {
        $encoded = json_encode($event->context) ?: '';

        if (str_contains($event->message, 'super-secret-sql-password') || str_contains($encoded, 'super-secret-sql-password')) {
            $logged = true;
        }
    });

    Route::get('/api/v1/_test/boom', function (): never {
        throw new RuntimeException('super-secret-sql-password');
    });

    $this->getJson('/api/v1/_test/boom')
        ->assertStatus(500)
        ->assertExactJson([
            'error' => [
                'code' => 'internal_error',
                'message' => 'Unexpected error.',
            ],
        ]);

    expect($logged)->toBeTrue();
});
