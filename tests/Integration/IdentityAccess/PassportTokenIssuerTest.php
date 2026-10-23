<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Src\IdentityAccess\Domain\TokenIssuer;
use Src\Shared\Domain\UserId;

test('passport issuer creates a token that expires in 30 minutes', function (): void {
    $this->createPersonalAccessClient();
    $user = User::factory()->create();
    $expectedExpiration = Carbon::now()->addMinutes(30);

    $token = $this->app->make(TokenIssuer::class)->issueFor(new UserId($user->id));

    $storedExpiration = Carbon::parse(
        DB::table('oauth_access_tokens')->where('user_id', $user->id)->value('expires_at'),
    );

    expect($token->value())->not->toBeEmpty()
        ->and(abs($storedExpiration->diffInSeconds($expectedExpiration)))->toBeLessThanOrEqual(2)
        ->and($token->expiresAt()->getTimestamp())->toBe($storedExpiration->getTimestamp());
});
