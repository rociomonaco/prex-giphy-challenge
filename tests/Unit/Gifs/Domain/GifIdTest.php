<?php

declare(strict_types=1);

use Src\Gifs\Domain\GifId;
use Src\Gifs\Domain\InvalidGifId;

test('gif id rejects non alphanumeric values', function (string $value): void {
    new GifId($value);
})->throws(InvalidGifId::class)->with([
    'empty' => '',
    'blank' => '   ',
    'contains a dash' => 'abc-123',
    'contains an underscore' => 'abc_123',
    'contains a slash' => 'abc/123',
    'longer than 64 characters' => str_repeat('a', 65),
]);
