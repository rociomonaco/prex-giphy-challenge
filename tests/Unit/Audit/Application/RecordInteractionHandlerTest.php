<?php

declare(strict_types=1);

use Src\Audit\Application\RecordInteraction\RecordInteractionCommand;
use Src\Audit\Application\RecordInteraction\RecordInteractionHandler;
use Src\Audit\Domain\SensitiveDataMasker;
use Tests\Support\Fakes\FixedClock;
use Tests\Support\Fakes\InMemoryAuditLogRepository;

beforeEach(function (): void {
    $this->now = new DateTimeImmutable('2026-10-23T15:00:00+00:00');
    $this->repository = new InMemoryAuditLogRepository;
    $this->handler = new RecordInteractionHandler(
        $this->repository,
        new FixedClock($this->now),
        new SensitiveDataMasker,
    );
});

test('record interaction masks bodies before saving', function (): void {
    $this->handler->handle(new RecordInteractionCommand(
        userId: 7,
        service: 'auth.login',
        method: 'POST',
        path: '/api/auth/login',
        requestBody: [
            'email' => 'ada@example.com',
            'password' => 'secret',
        ],
        responseStatus: 200,
        responseBody: '{"access_token":"tok","token_type":"Bearer"}',
        ip: '10.0.0.8',
    ));

    $saved = $this->repository->all();

    expect($saved)->toHaveCount(1);

    $log = $saved[0];

    expect($log->userId?->value())->toBe(7)
        ->and($log->service)->toBe('auth.login')
        ->and($log->method)->toBe('POST')
        ->and($log->path)->toBe('/api/auth/login')
        ->and($log->requestBody)->toBe([
            'email' => 'ada@example.com',
            'password' => '***',
        ])
        ->and($log->responseStatus)->toBe(200)
        ->and($log->responseBody)->toBe('{"access_token":"***","token_type":"Bearer"}')
        ->and($log->ipAddress)->toBe('10.0.0.8')
        ->and($log->occurredAt)->toEqual($this->now);

    $this->handler->handle(new RecordInteractionCommand(
        userId: null,
        service: 'auth.login',
        method: 'POST',
        path: '/api/auth/login',
        requestBody: ['email' => 'ada@example.com', 'password' => 'wrong'],
        responseStatus: 401,
        responseBody: '{"error":{"code":"invalid_credentials"}}',
        ip: '10.0.0.8',
    ));

    $failed = $this->repository->all()[1];

    expect($failed->userId)->toBeNull()
        ->and($failed->requestBody)->toBe([
            'email' => 'ada@example.com',
            'password' => '***',
        ])
        ->and($failed->responseStatus)->toBe(401)
        ->and($failed->occurredAt)->toEqual($this->now);
});
