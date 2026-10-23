<?php

declare(strict_types=1);

namespace Src\IdentityAccess\Domain;

use Src\Shared\Domain\InvalidArgument;

final class InvalidAccessToken extends InvalidArgument
{
    public function __construct()
    {
        parent::__construct('Access token cannot be empty.');
    }
}
