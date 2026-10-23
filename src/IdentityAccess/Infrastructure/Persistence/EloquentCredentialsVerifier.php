<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Infrastructure\Persistence;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Src\IdentityAccess\Domain\CredentialsVerifier;
use Src\IdentityAccess\Domain\Email;
use Src\IdentityAccess\Domain\InvalidCredentials;
use Src\Shared\Domain\UserId;

final class EloquentCredentialsVerifier implements CredentialsVerifier
{
    public function verify(Email $email, string $plainPassword): UserId
    {
        $user = User::query()->where('email', $email->value())->first();

        if ($user === null || ! Hash::check($plainPassword, $user->password)) {
            throw new InvalidCredentials();
        }

        return new UserId((int) $user->getKey());
    }
}
