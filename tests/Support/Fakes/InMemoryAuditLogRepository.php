<?php

declare(strict_types=1);

namespace Tests\Support\Fakes;

use Src\Audit\Domain\AuditLog;
use Src\Audit\Domain\AuditLogRepository;

final class InMemoryAuditLogRepository implements AuditLogRepository
{
    /** @var list<AuditLog> */
    private array $logs = [];

    public function save(AuditLog $auditLog): void
    {
        $this->logs[] = $auditLog;
    }

    /**
     * @return list<AuditLog>
     */
    public function all(): array
    {
        return $this->logs;
    }
}
