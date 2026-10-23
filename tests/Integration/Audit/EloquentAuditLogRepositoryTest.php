<?php

declare(strict_types=1);

use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Facades\Schema;
use Src\Audit\Domain\AuditLog;
use Src\Audit\Domain\AuditLogRepository;
use Src\Audit\Infrastructure\Persistence\AuditLogModel;
use Src\Shared\Domain\UserId;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->repository = $this->app->make(AuditLogRepository::class);
});

test('eloquent audit repository persists every field', function (): void {
    $occurredAt = new DateTimeImmutable('2026-10-23T15:04:05+00:00');
    $requestBody = ['q' => 'cats', 'password' => '***'];
    $responseBody = '{"data":[],"access_token":"***"}';

    $this->repository->save(new AuditLog(
        userId: new UserId($this->user->id),
        service: 'gifs.search',
        method: 'GET',
        path: '/api/gifs',
        requestBody: $requestBody,
        responseStatus: 200,
        responseBody: $responseBody,
        ipAddress: '0000:0000:0000:0000:0000:ffff:192.168.100.228',
        occurredAt: $occurredAt,
    ));

    $stored = AuditLogModel::query()->sole();

    expect($stored->user_id)->toBe($this->user->id)
        ->and($stored->service)->toBe('gifs.search')
        ->and($stored->method)->toBe('GET')
        ->and($stored->path)->toBe('/api/gifs')
        ->and($stored->request_body)->toEqual($requestBody)
        ->and($stored->response_status)->toBe(200)
        ->and($stored->response_body)->toBe($responseBody)
        ->and($stored->ip_address)->toBe('0000:0000:0000:0000:0000:ffff:192.168.100.228')
        ->and($stored->created_at->toDateTimeImmutable()->format(DateTimeInterface::ATOM))
        ->toBe($occurredAt->format(DateTimeInterface::ATOM))
        ->and(Schema::hasColumn('audit_logs', 'updated_at'))->toBeFalse();

    $this->repository->save(new AuditLog(
        userId: null,
        service: 'auth.login',
        method: 'POST',
        path: '/api/auth/login',
        requestBody: ['email' => 'ada@example.com', 'password' => '***'],
        responseStatus: 401,
        responseBody: '{"error":{"code":"invalid_credentials"}}',
        ipAddress: '203.0.113.10',
        occurredAt: $occurredAt,
    ));

    expect(AuditLogModel::query()->whereNull('user_id')->sole()->response_status)->toBe(401);

    $this->user->delete();

    expect(AuditLogModel::query()->findOrFail($stored->id)->user_id)->toBeNull();

    $indexedColumns = collect(Schema::getIndexes('audit_logs'))
        ->flatMap(fn (array $index): array => $index['columns'])
        ->all();

    expect($indexedColumns)->toContain('user_id', 'service', 'created_at');
});
