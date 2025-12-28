<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Tests\Builders\AccountBuilder;
use App\Tests\Builders\CompanyBuilder;
use PHPUnit\Framework\TestCase;

final class CompanyTest extends TestCase
{
    public function testCompanyIsCreatedWithProvidedValues(): void
    {
        $ownerId = '11111111-1111-1111-1111-111111111111';
        $companyId = '22222222-2222-2222-2222-222222222222';
        $createdAt = new \DateTimeImmutable('2025-02-01 10:00:00');

        $ownerBuilder = (new AccountBuilder())
            ->withId($ownerId)
            ->withEmail('owner@conwix.test')
            ->withCreatedAt(new \DateTimeImmutable('2025-02-01 09:00:00'));

        $company = (new CompanyBuilder())
            ->withId($companyId)
            ->withName('My Company')
            ->withCreatedAt($createdAt)
            ->withOwnerBuilder($ownerBuilder)
            ->build();

        self::assertSame($companyId, $company->getId());
        self::assertSame('My Company', $company->getName());
        self::assertEquals($createdAt, $company->getCreatedAt());
        self::assertSame($ownerId, $company->getOwner()->getId());
    }
}
