<?php

declare(strict_types=1);

namespace Src\Audit\Domain;

interface AuditLogRepository
{
    public function save(AuditLog $auditLog): void;
}
