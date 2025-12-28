<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Tests\Builders\ConversationBuilder;
use PHPUnit\Framework\TestCase;

final class ConversationTest extends TestCase
{
    public function testConversationIsCreatedWithProvidedValues(): void
    {
        $createdAt = new \DateTimeImmutable('2025-02-01 10:00:00');

        $conversation = (new ConversationBuilder())
            ->withId('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb')
            ->withCreatedAt($createdAt)
            ->build();

        self::assertSame('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', $conversation->getId());
        self::assertEquals($createdAt, $conversation->getCreatedAt());
        self::assertNotEmpty($conversation->getCompany()->getId());
        self::assertNotEmpty($conversation->getClient()->getId());
    }
}

