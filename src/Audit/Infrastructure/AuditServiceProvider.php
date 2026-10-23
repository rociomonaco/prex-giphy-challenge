<?php

declare(strict_types=1);

namespace Src\Audit\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Src\Audit\Domain\AuditLogRepository;
use Src\Audit\Infrastructure\Http\AuditInteraction;
use Src\Audit\Infrastructure\Persistence\EloquentAuditLogRepository;

final class AuditServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        AuditLogRepository::class => EloquentAuditLogRepository::class,
    ];

    public function register(): void
    {
        $this->app->singleton(AuditInteraction::class);
    }
}
