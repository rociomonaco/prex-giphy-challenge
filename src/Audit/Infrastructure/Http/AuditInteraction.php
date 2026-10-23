<?php

declare(strict_types=1);

namespace Src\Audit\Infrastructure\Http;

use Closure;
use Illuminate\Http\Request;
use Src\Audit\Application\RecordInteraction\RecordInteractionCommand;
use Src\Audit\Application\RecordInteraction\RecordInteractionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AuditInteraction
{
    public function __construct(private readonly RecordInteractionHandler $handler) {}

    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            $this->handler->handle(new RecordInteractionCommand(
                userId: $this->userId($request),
                service: $this->service($request),
                method: $request->getMethod(),
                path: $request->getPathInfo(),
                requestBody: $request->all(),
                responseStatus: $response->getStatusCode(),
                responseBody: $response->getContent() ?: '',
                ip: (string) $request->ip(),
            ));
        } catch (Throwable $exception) {
            $this->reportQuietly($exception);
        }
    }

    private function userId(Request $request): ?int
    {
        $id = $request->user('api')?->id ?? $request->attributes->get('audit.user_id');

        if (! is_numeric($id)) {
            return null;
        }

        return (int) $id;
    }

    private function service(Request $request): string
    {
        $name = $request->route()?->getName();

        if (is_string($name) && $name !== '') {
            return $name;
        }

        return $request->path();
    }

    private function reportQuietly(Throwable $exception): void
    {
        try {
            report($exception);
        } catch (Throwable) {
        }
    }
}
