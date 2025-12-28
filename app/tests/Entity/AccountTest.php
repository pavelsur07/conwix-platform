<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Tests\Builders\AccountBuilder;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    public function testAccountIsCreatedWithProvidedValues(): void
    {
        $id = '11111111-1111-1111-1111-111111111111';
        $email = 'owner@conwix.test';
        $createdAt = new \DateTimeImmutable('2025-02-01 10:00:00');

        $account = (new AccountBuilder())
            ->withId($id)
            ->withEmail($email)
            ->withCreatedAt($createdAt)
            ->build();

        self::assertSame($id, $account->getId());
        self::assertSame($email, $account->getEmail());
        self::assertEquals($createdAt, $account->getCreatedAt());
    }
}
