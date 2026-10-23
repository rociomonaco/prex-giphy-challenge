<?php

declare(strict_types=1);

namespace Tests\Support;

use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

trait InteractsWithPassport
{
    protected function createPersonalAccessClient(): Client
    {
        return $this->app->make(ClientRepository::class)
            ->createPersonalAccessGrantClient('Test Personal Access Client', 'users');
    }
}
