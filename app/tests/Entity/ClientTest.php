<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Tests\Builders\ClientBuilder;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testClientIsCreatedWithProvidedValues(): void
    {
        $client = (new ClientBuilder())
            ->withId('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa')
            ->withExternalId('client-xyz')
            ->withCreatedAt(new \DateTimeImmutable('2025-02-01 10:00:00'))
            ->build();

        self::assertSame('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', $client->getId());
        self::assertSame('client-xyz', $client->getExternalId());
        self::assertEquals(new \DateTimeImmutable('2025-02-01 10:00:00'), $client->getCreatedAt());
    }
}

