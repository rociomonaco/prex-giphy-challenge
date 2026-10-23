<?php

declare(strict_types=1);

namespace Src\Audit\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'service',
    'method',
    'path',
    'request_body',
    'response_status',
    'response_body',
    'ip_address',
    'created_at',
])]
final class AuditLogModel extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'audit_logs';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'request_body' => 'array',
            'response_status' => 'integer',
        ];
    }
}
