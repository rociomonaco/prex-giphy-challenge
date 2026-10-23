<?php

declare(strict_types=1);

namespace Src\Audit\Infrastructure\Persistence;

use Src\Audit\Domain\AuditLog;
use Src\Audit\Domain\AuditLogRepository;

final class EloquentAuditLogRepository implements AuditLogRepository
{
    public function save(AuditLog $auditLog): void
    {
        AuditLogModel::query()->create([
            'user_id' => $auditLog->userId?->value(),
            'service' => $auditLog->service,
            'method' => $auditLog->method,
            'path' => $auditLog->path,
            'request_body' => $auditLog->requestBody,
            'response_status' => $auditLog->responseStatus,
            'response_body' => $auditLog->responseBody,
            'ip_address' => $auditLog->ipAddress,
            'created_at' => $auditLog->occurredAt,
        ]);
    }
}
