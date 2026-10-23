<?php

declare(strict_types=1);

const CONTEXTS = ['IdentityAccess', 'Gifs', 'Favorites', 'Audit', 'Shared'];

// `not` on an array subject passes as soon as one namespace is clean, so each namespace gets its own expectation.

arch('domain layer does not depend on the framework', function (): void {
    foreach (CONTEXTS as $context) {
        expect("Src\\{$context}\\Domain")->not->toUse(['Illuminate', 'Laravel']);
    }
});

arch('application layer does not depend on infrastructure', function (): void {
    $infrastructure = array_map(fn (string $context): string => "Src\\{$context}\\Infrastructure", CONTEXTS);

    foreach (CONTEXTS as $context) {
        expect("Src\\{$context}\\Application")->not->toUse([...$infrastructure, 'Illuminate']);
    }
});

arch('source code uses strict types')
    ->expect('Src')
    ->toUseStrictTypes();

arch('debug functions are not used', function (): void {
    foreach (['Src', 'App'] as $namespace) {
        expect($namespace)->not->toUse(['dd', 'dump', 'var_dump', 'ray']);
    }
});
