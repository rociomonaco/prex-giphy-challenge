<?php

declare(strict_types=1);

namespace Src\Audit\Application\RecordInteraction;

final readonly class RecordInteractionCommand
{
    /**
     * @param  array<array-key, mixed>  $requestBody
     */
    public function __construct(
        public ?int $userId,
        public string $service,
        public string $method,
        public string $path,
        public array $requestBody,
        public int $responseStatus,
        public string $responseBody,
        public string $ip,
    ) {}
}
