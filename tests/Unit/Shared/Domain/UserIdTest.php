<?php

declare(strict_types=1);

use Src\Shared\Domain\InvalidUserId;
use Src\Shared\Domain\UserId;

test('user id rejects non positive values', function (int $value): void {
    new UserId($value);
})->throws(InvalidUserId::class)->with([0, -1]);
