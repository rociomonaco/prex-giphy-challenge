<?php

declare(strict_types=1);

use Src\Favorites\Domain\Alias;
use Src\Favorites\Domain\InvalidAlias;

test('alias is trimmed', function (): void {
    $alias = new Alias('  Funny cat  ');

    expect($alias->value())->toBe('Funny cat');
});

test('alias rejects empty or too long values', function (string $value): void {
    new Alias($value);
})->throws(InvalidAlias::class)->with([
    'empty' => '',
    'blank' => '   ',
    'longer than 255 characters' => str_repeat('a', 256),
    'longer than 255 multibyte characters' => str_repeat('ñ', 256),
]);
