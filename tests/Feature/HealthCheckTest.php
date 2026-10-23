<?php

declare(strict_types=1);

test('the application boots and the health endpoint responds', function (): void {
    $this->get('/up')->assertOk();
});
