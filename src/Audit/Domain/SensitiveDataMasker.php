<?php

declare(strict_types=1);

namespace Src\Audit\Domain;

final class SensitiveDataMasker
{
    private const MASK = '***';

    /** @var list<string> */
    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'access_token',
        'token',
        'api_key',
        'authorization',
    ];

    /**
     * @param  array<array-key, mixed>|string  $value
     * @return array<array-key, mixed>|string
     */
    public function mask(array|string $value): array|string
    {
        if (is_string($value)) {
            return $this->maskJsonString($value);
        }

        return $this->maskArray($value);
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    private function maskArray(array $data): array
    {
        $masked = [];

        foreach ($data as $key => $value) {
            if (is_string($key) && $this->isSensitive($key)) {
                $masked[$key] = self::MASK;

                continue;
            }

            $masked[$key] = $this->maskValue($value);
        }

        return $masked;
    }

    private function maskValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $this->maskArray($value);
        }

        if (is_string($value)) {
            return $this->maskJsonString($value);
        }

        return $value;
    }

    private function maskJsonString(string $value): string
    {
        if (! json_validate($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            return $value;
        }

        return json_encode(
            $this->maskArray($decoded),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );
    }

    private function isSensitive(string $key): bool
    {
        return in_array(strtolower($key), self::SENSITIVE_KEYS, true);
    }
}
