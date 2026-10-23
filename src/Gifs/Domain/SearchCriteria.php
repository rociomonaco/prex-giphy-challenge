<?php

declare(strict_types=1);

namespace Src\Gifs\Domain;

final readonly class SearchCriteria
{
    public const MAX_QUERY_LENGTH = 50;
    public const MIN_LIMIT = 1;
    public const MAX_LIMIT = 50;
    public const DEFAULT_LIMIT = 25;
    public const MIN_OFFSET = 0;
    public const MAX_OFFSET = 4999;
    public const DEFAULT_OFFSET = 0;

    private string $query;

    public function __construct(
        string $query,
        private int $limit = self::DEFAULT_LIMIT,
        private int $offset = self::DEFAULT_OFFSET,
    ) {
        $query = trim($query);

        if ($query === '') {
            throw InvalidSearchCriteria::emptyQuery();
        }

        if (mb_strlen($query) > self::MAX_QUERY_LENGTH) {
            throw InvalidSearchCriteria::queryTooLong();
        }

        if ($limit < self::MIN_LIMIT || $limit > self::MAX_LIMIT) {
            throw InvalidSearchCriteria::limitOutOfRange();
        }

        if ($offset < self::MIN_OFFSET || $offset > self::MAX_OFFSET) {
            throw InvalidSearchCriteria::offsetOutOfRange();
        }

        $this->query = $query;
    }

    public function query(): string
    {
        return $this->query;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return $this->offset;
    }
}
