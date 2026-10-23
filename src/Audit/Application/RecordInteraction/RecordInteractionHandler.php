<?php

declare(strict_types=1);

namespace Src\Audit\Application\RecordInteraction;

use Src\Audit\Domain\AuditLog;
use Src\Audit\Domain\AuditLogRepository;
use Src\Audit\Domain\InvalidAuditLog;
use Src\Audit\Domain\SensitiveDataMasker;
use Src\Shared\Domain\Clock;
use Src\Shared\Domain\InvalidUserId;
use Src\Shared\Domain\UserId;

final readonly class RecordInteractionHandler
{
    public function __construct(
        private AuditLogRepository $auditLogs,
        private Clock $clock,
        private SensitiveDataMasker $masker,
    ) {}

    /**
     * @throws InvalidUserId
     * @throws InvalidAuditLog
     */
    public function handle(RecordInteractionCommand $command): void
    {
        $requestBody = $this->masker->mask($command->requestBody);
        $responseBody = $this->masker->mask($command->responseBody);

        if (! is_array($requestBody) || ! is_string($responseBody)) {
            throw new \LogicException('SensitiveDataMasker must preserve the body type.');
        }

        $this->auditLogs->save(new AuditLog(
            userId: $command->userId === null ? null : new UserId($command->userId),
            service: $command->service,
            method: $command->method,
            path: $command->path,
            requestBody: $requestBody,
            responseStatus: $command->responseStatus,
            responseBody: $responseBody,
            ipAddress: $command->ip,
            occurredAt: $this->clock->now(),
        ));
    }
}
