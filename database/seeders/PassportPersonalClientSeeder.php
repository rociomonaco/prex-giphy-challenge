<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use RuntimeException;

final class PassportPersonalClientSeeder extends Seeder
{
    private const PROVIDER = 'users';

    public function run(ClientRepository $clients): void
    {
        try {
            $clients->personalAccessClient(self::PROVIDER);
        } catch (RuntimeException) {
            $clients->createPersonalAccessGrantClient('Personal Access Client', self::PROVIDER);
        }
    }
}
