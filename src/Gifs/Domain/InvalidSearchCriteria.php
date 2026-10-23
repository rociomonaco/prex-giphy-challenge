<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

use Src\Shared\Domain\InvalidArgument;

final class InvalidSearchCriteria extends InvalidArgument
{
    public static function emptyQuery(): self
    {
        return new self('Query cannot be empty.');
    }

    public static function queryTooLong(): self
    {
        return new self(sprintf('Query cannot be longer than %d characters.', SearchCriteria::MAX_QUERY_LENGTH));
    }

    public static function limitOutOfRange(): self
    {
        return new self(sprintf('Limit must be between %d and %d.', SearchCriteria::MIN_LIMIT, SearchCriteria::MAX_LIMIT));
    }

    public static function offsetOutOfRange(): self
    {
        return new self(sprintf('Offset must be between %d and %d.', SearchCriteria::MIN_OFFSET, SearchCriteria::MAX_OFFSET));
    }
}
