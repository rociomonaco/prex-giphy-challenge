<?php

declare(strict_types=1);

use Src\IdentityAccess\Domain\Email;
use Src\IdentityAccess\Domain\InvalidEmail;

test('email is normalized to lowercase', function (): void {
    $email = new Email('  Test@PREX.com ');

    expect($email->value())->toBe('test@prex.com');
});

test('email rejects invalid formats', function (string $value): void {
    new Email($value);
})->throws(InvalidEmail::class, 'Invalid email address.')->with([
    'empty' => '',
    'blank' => '   ',
    'missing at' => 'test.prex.com',
    'missing domain' => 'test@',
    'missing local part' => '@prex.com',
    'double at' => 'test@@prex.com',
    'contains spaces' => 'te st@prex.com',
]);
