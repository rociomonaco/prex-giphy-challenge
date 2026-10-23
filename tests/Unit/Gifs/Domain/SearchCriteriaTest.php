<?php

declare(strict_types=1);

use Src\Gifs\Domain\InvalidSearchCriteria;
use Src\Gifs\Domain\SearchCriteria;

test('search criteria applies default limit and offset', function (): void {
    $criteria = new SearchCriteria('  cats ');

    expect($criteria->query())->toBe('cats')
        ->and($criteria->limit())->toBe(25)
        ->and($criteria->offset())->toBe(0);
});

test('search criteria rejects an empty query', function (string $query): void {
    new SearchCriteria($query);
})->throws(InvalidSearchCriteria::class, 'Query cannot be empty.')->with([
    'empty' => '',
    'blank' => '   ',
]);

test('search criteria rejects an out of range limit', function (int $limit): void {
    new SearchCriteria('cats', limit: $limit);
})->throws(InvalidSearchCriteria::class, 'Limit must be between 1 and 50.')->with([0, 51]);

test('search criteria rejects an out of range offset', function (int $offset): void {
    new SearchCriteria('cats', offset: $offset);
})->throws(InvalidSearchCriteria::class, 'Offset must be between 0 and 4999.')->with([-1, 5000]);
