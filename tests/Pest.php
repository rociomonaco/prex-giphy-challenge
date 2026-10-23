<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithPassport;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class, InteractsWithPassport::class)
    ->in('Feature', 'Integration');
