<?php

declare(strict_types=1);

use Src\Audit\Domain\AuditLog;
use Src\Audit\Domain\InvalidAuditLog;
use Src\Shared\Domain\UserId;

test('audit log rejects an invalid http status', function (): void {
    $log = function (int $status, ?UserId $userId = null): AuditLog {
        return new AuditLog(
            userId: $userId,
            service: 'gifs.search',
            method: 'GET',
            path: '/api/gifs',
            requestBody: ['q' => 'cats'],
            responseStatus: $status,
            responseBody: '{}',
            ipAddress: '127.0.0.1',
            occurredAt: new DateTimeImmutable('2026-10-23T15:00:00+00:00'),
        );
    };

    expect(fn () => $log(99))->toThrow(InvalidAuditLog::class)
        ->and(fn () => $log(600))->toThrow(InvalidAuditLog::class)
        ->and(fn () => $log(0))->toThrow(InvalidAuditLog::class)
        ->and(fn () => $log(-1))->toThrow(InvalidAuditLog::class);

    $accepted = $log(100);

    expect($accepted->responseStatus)->toBe(100)
        ->and($accepted->userId)->toBeNull()
        ->and($log(599, new UserId(1))->responseStatus)->toBe(599);
});
