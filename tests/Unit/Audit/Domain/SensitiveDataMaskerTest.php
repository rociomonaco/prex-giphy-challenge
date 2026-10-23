<?php

declare(strict_types=1);

use Src\Audit\Domain\SensitiveDataMasker;

test('masker hides sensitive fields recursively', function (): void {
    $masker = new SensitiveDataMasker;

    $masked = $masker->mask([
        'email' => 'ada@example.com',
        'password' => 'secret',
        'nested' => [
            'API_KEY' => 'giphy-key',
            'profile' => [
                'access_token' => 'tok',
                'name' => 'Ada',
            ],
        ],
        'headers' => [
            'Authorization' => 'Bearer abc',
        ],
        'Password_Confirmation' => 'secret',
        'payload' => '{"token":"abc","password_confirmation":"secret","q":"cats"}',
        'note' => 'not json',
    ]);

    expect($masked)->toBe([
        'email' => 'ada@example.com',
        'password' => '***',
        'nested' => [
            'API_KEY' => '***',
            'profile' => [
                'access_token' => '***',
                'name' => 'Ada',
            ],
        ],
        'headers' => [
            'Authorization' => '***',
        ],
        'Password_Confirmation' => '***',
        'payload' => '{"token":"***","password_confirmation":"***","q":"cats"}',
        'note' => 'not json',
    ]);
});

test('masker keeps non sensitive fields', function (): void {
    $masker = new SensitiveDataMasker;

    $input = [
        'q' => 'cats',
        'limit' => 25,
        'offset' => 0,
        'gif_id' => 'abc123',
        'token_type' => 'Bearer',
        'items' => [
            ['id' => 'abc123', 'title' => 'Cat'],
        ],
        'empty' => null,
    ];

    expect($masker->mask($input))->toBe($input)
        ->and($masker->mask('plain text'))->toBe('plain text')
        ->and($masker->mask('{"q":"cats"}'))->toBe('{"q":"cats"}');
});
