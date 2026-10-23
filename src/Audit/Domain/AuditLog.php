<?php

declare(strict_types=1);

namespace Src\Audit\Domain;

use DateTimeImmutable;
use Src\Shared\Domain\UserId;

final readonly class AuditLog
{
    /**
     * @param  array<array-key, mixed>  $requestBody
     */
    public function __construct(
        public ?UserId $userId,
        public string $service,
        public string $method,
        public string $path,
        public array $requestBody,
        public int $responseStatus,
        public string $responseBody,
        public string $ipAddress,
        public DateTimeImmutable $occurredAt,
    ) {
        if ($responseStatus < 100 || $responseStatus > 599) {
            throw InvalidAuditLog::status($responseStatus);
        }
    }
}
