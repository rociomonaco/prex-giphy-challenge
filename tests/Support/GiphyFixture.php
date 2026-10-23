<?php

declare(strict_types=1);

namespace Tests\Support;

use JsonException;

final class GiphyFixture
{
    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    public static function load(string $name): array
    {
        $contents = file_get_contents(dirname(__DIR__)."/Fixtures/giphy/{$name}.json");

        return json_decode((string) $contents, true, flags: JSON_THROW_ON_ERROR);
    }
}
