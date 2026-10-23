<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'test@prex.com'],
            ['name' => 'Test User', 'password' => Hash::make('password')],
        );
    }
}
